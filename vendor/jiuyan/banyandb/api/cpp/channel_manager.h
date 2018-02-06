#ifndef BANYAN_CPP_CHANNEL_MANAGER_H_
#define BANYAN_CPP_CHANNEL_MANAGER_H_

#include <common/banyan_common.h>

#include <list>
#include <vector>
#include <string>
#include "channel.h"

namespace banyan {
;

#define INIT_CHANNEL_NUM        2
#define DEFAULT_CHANNEL_NUM     64
#define MAX_CHANNEL_NUM         128

/*  Class Instance
 * each instance express a backend service
 */
class Instance {
public:
    Instance();
    ~Instance();
    
    int Init(const uint32_t instance_id, std::string &ip, int port, const uint16_t init_num, const uint16_t default_num, const uint16_t max_num);

    Channel* BorrowChannel();

    void ReturnChannel(Channel* channel, uint32_t op_count, uint32_t fail_count, bool drop);

    int Start(bool async);

    void Stop();

    uint32_t GetId();

    bool IsValid();

    float GetFailRatio();

private:
    std::string             _ip;
    std::list<Channel *>    _channels;
    int                     _port;

    MutexLock               _lock;

    bool                    _is_avaliable;
    uint32_t                _instance_id;
    uint32_t                _epoch;
    uint64_t                _fail_count;
    uint64_t                _query_count;

    uint32_t                _init_count;
    uint32_t                _total_count;
    uint32_t                _default_count;
    uint32_t                _max_count;
};


/*  Class ChannelManager
 *
 */
class ChannelManager {
public:
    ChannelManager();

    ~ChannelManager();

    static ChannelManager* GetInstance() { 
        static ChannelManager cm;

        return &cm;
    }

    int Init(const std::string hosts);

    void Destroy();      

    Channel* BorrowChannel();

    void ReturnChannel(Channel *channel, int op_count, int faile_count, bool drop);
        
private:
    void AddInstance(size_t index, std::string &ip_port);

    void RemoveInstance(size_t index);


private:
    std::vector<Instance *> _instances;
};

}
#endif // BANYAN_CPP_CLUSTER_LINK_H_
