#include "channel_manager.h"
#include <vector>

namespace banyan {

/*
 * Class Instance implements
 */
Instance::Instance() {
}

int Instance::Init(const uint32_t instance_id, std::string &ip, int port, uint16_t init_num, uint16_t default_num, uint16_t max_num) {
    _ip = ip;
    _port = port;
    _instance_id = instance_id;

    _epoch = 0;
    _fail_count = 0;
    _query_count = 0;

    _total_count = 0;
    _init_count = init_num;
    _default_count = default_num;
    _max_count = max_num;

    this->Start(false);

    return 0;
}

Instance::~Instance() {
    this->Stop();
}

int Instance::Start(bool /*async*/) {

    AutoLocker<MutexLock> lock(&_lock);
    uint32_t channel_num = (_init_count > _total_count) ? (_init_count - _total_count) : 0;

    bool can_connect = false;
    for (uint32_t i = 0; i < channel_num; i++) {
        int sock = tcp_create_socket_timeout(_ip.c_str(), _port, 1000);
        if (sock != -1) {
            char buf[64];
            int len = snprintf(buf, sizeof(buf), "%s:%d", _ip.c_str(), _port);
            std::string peer(buf, len);
            Channel *channel = new Channel(sock, peer, _epoch, _instance_id);
            if (channel != NULL) {
                _channels.push_back(channel);
                _total_count++;
                can_connect = true;
            }
        }
    }

    if  (can_connect && _total_count >= _init_count) {
        _is_avaliable = true;
    }

    _fail_count = 0;
    _query_count = 0;
    BANYAN_LOG(LOG_INFO, "instance started:[%s:%d], [total count:%d], [default count:%d], [avaliable:%s]",
        _ip.c_str(), _port, _total_count, _default_count, (_is_avaliable ? "yes" : "no") );

    return 0;
}

uint32_t Instance::GetId() {
    return _instance_id;
}

Channel* Instance::BorrowChannel() {

    // borrow from channel list firstly
    BANYAN_LOG(LOG_DEBUG, "borrow channel [%s:%d], [total_num:%d], [default_num:%d], [max_num:%d]",
        _ip.c_str(), _port, _total_count, _default_count, _max_count);

    AutoLocker<MutexLock> lock(&_lock);
    while (!_channels.empty() ) {
        Channel *channel = _channels.front();
        _channels.pop_front();

        if (channel->GetEpoch() != _epoch) {
            delete channel;
            _total_count--;
        }

        if (channel->IsDown() ) {
            _query_count++;
            _fail_count++;

            delete channel;
            _total_count--;

            continue;
        }

        return channel;
    }

    if (_total_count >= _max_count) {
        BANYAN_LOG(LOG_ERROR, "borrow upstream failed, reach max count, [%s:%d], [total_num:%d], [default_num:%d], [max_num:%d]",
            _ip.c_str(), _port, _total_count, _default_count, _max_count);

        return NULL;
    }

    int sock = tcp_create_socket_timeout(_ip.c_str(), _port, 1000);
    if (sock == -1) {
        BANYAN_LOG(LOG_ERROR, "borrow upstream failed, create socket error, [%s:%d], [total_num:%d], [default_num:%d], [max_num:%d], [err:%s]",
            _ip.c_str(), _port, _total_count, _default_count, _max_count, strerror(errno) );
    }

    char buf[64];
    int len = snprintf(buf, sizeof(buf), "%s:%d", _ip.c_str(), _port);
    std::string peer(buf, len);
    Channel *channel = new Channel(sock, peer, _epoch, _instance_id);
    if (channel != NULL) {
        _total_count++;
        return channel;
    }
    
    return NULL;
}

void Instance::ReturnChannel(Channel *channel, uint32_t op_count, uint32_t fail_count, bool drop) {

    AutoLocker<MutexLock> lock(&_lock);
    if (_epoch != channel->GetEpoch() || _instance_id != channel->GetGroupId() ) {
        delete channel;
        _total_count--;

        return;
    }

    _query_count += op_count;
    _fail_count += fail_count;

    if (_total_count < _default_count && !drop) {
        _channels.push_back(channel);
        return;
    }

    delete channel;
    _total_count--;
    return;
}

void Instance::Stop() {

    AutoLocker<MutexLock> lock(&_lock);
    for (auto iter = _channels.begin(); iter != _channels.end(); iter++) {
        delete *iter;
        _total_count--;
    }

    _channels.clear();
    _query_count = 0;
    _fail_count = 0;

    ++_epoch;
}

float Instance::GetFailRatio() {
    return ((float)_fail_count / _query_count);
}

bool Instance::IsValid() {
    return (GetFailRatio() < 0.4f);
}


/*
 *  Class ChannelManager implements
 *
 */
ChannelManager::ChannelManager() {
}

ChannelManager::~ChannelManager() {
}


void ChannelManager::Destroy() {
    for (auto iter = _instances.begin(); iter != _instances.end(); iter++) {
        delete *iter;
        *iter = NULL;
    }

    _instances.clear();
}

int ChannelManager::Init(const std::string hosts) {
    std::vector<std::string> ip_ports = str_split(hosts, ',');

    _instances.clear();
    _instances.resize(ip_ports.size(), NULL);
    for (size_t i = 0; i < ip_ports.size(); i++) {
        AddInstance(i, ip_ports[i]);
    }

    return 0;
}

void ChannelManager::AddInstance(size_t index, std::string &ip_port) {
    std::string ip, port;
    str_to_kv(ip_port, ':', &ip, &port);

    Instance *instance = new Instance();
    int res = instance->Init(index, ip, str_to_uint32(port), INIT_CHANNEL_NUM, DEFAULT_CHANNEL_NUM, MAX_CHANNEL_NUM);
    if (res == 0) {
        _instances[index] = instance;
    } else {
        delete instance;
    }
    return;
} 

void ChannelManager::RemoveInstance(size_t index) {
    if (index >= _instances.size() ) {
        return;
    }

    Instance *instance = _instances[index];

    if (instance != NULL) {
        delete instance;
        _instances[index] = NULL;
    }
}

Channel* ChannelManager::BorrowChannel() { 
    Instance *instance = NULL;

    instance = _instances[0];
    return instance->BorrowChannel();
}

void ChannelManager::ReturnChannel(Channel* channel, int op_count, int fail_count, bool drop) {
    size_t group = channel->GetGroupId();
    if (group < _instances.size() ) {
        Instance *instance = _instances[group];
        if (instance != NULL) {
            instance->ReturnChannel(channel, op_count, fail_count, drop);
        }
    }
}

}


