#include "banyan_api.h"

namespace banyan {
;

BanyanClient::BanyanClient() {
    _write_buf = new BytesQueue(8192, &_pool);
}


BanyanClient::~BanyanClient() {
    delete _write_buf;
}

int BanyanClient::Init(const std::string hosts, const std::string ns, const std::string table) {
    BANYAN_LOG(LOG_INFO, "Initializing client, ns:%s, table:%s, hosts:%s", ns.c_str(), table.c_str(), hosts.c_str() );
    _ns       = ns;
    _table    = table;

    _cm = ChannelManager::GetInstance();

    return _cm->Init(hosts);
}

void BanyanClient::SetNamespace(const std::string ns) {
    _ns = ns;
}

void BanyanClient::SetTable(const std::string table) {
    _table = table;
}

const std::string BanyanClient::GetNamespace() {
    return _ns;
}

const std::string BanyanClient::GetTable() {
    return _table;
}

int BanyanClient::Request(BytesRef cmd, Result &result) {
    BytesRefList_t params;
    params.push_back(cmd);

    return this->Request(params, result);
}

int BanyanClient::Request(BytesRef cmd, BytesRef param, Result &result) {
    BytesRefList_t params;
    params.push_back(cmd);
    params.push_back(param);

    return this->Request(params, result);
}

int BanyanClient::Request(BytesRef cmd, BytesRef param1, BytesRef param2, Result &result) {
    BytesRefList_t params;
    params.push_back(cmd);
    params.push_back(param1);
    params.push_back(param2);

    return this->Request(params, result);
}

int BanyanClient::Request(BytesRef cmd, BytesRef param1, BytesRef param2, BytesRef param3, Result &result) {
    BytesRefList_t params;
    params.push_back(cmd);
    params.push_back(param1);
    params.push_back(param2);
    params.push_back(param3);

    return this->Request(params, result);
}

int BanyanClient::Request(BytesRef cmd, BytesRef param1, BytesRef param2, BytesRef param3, BytesRef param4, Result &result) {
    BytesRefList_t params;
    params.push_back(cmd);
    params.push_back(param1);
    params.push_back(param2);
    params.push_back(param3);
    params.push_back(param4);

    return this->Request(params, result);
}

int BanyanClient::Request(BytesRefList_t &params, Result &result) {
#define DEFAULT_BUF_SIZE 512
    _request.options["ns"] = _ns;
    _request.options["tab"] = _table;
    _request.options["proto"] = "by";
    // _request.options["rid"] =
    _request.items.assign(params.begin(), params.end() );

    _write_buf->reset();
    int rc = BanyanProtocol::SerializeRequest(_request, _write_buf);
    if (rc != BanyanProtocol::OK) {
        BANYAN_LOG(LOG_ERROR, "serialize request failed, cmd:%s, key:%s, param size:%d", params[0].data(), params[1].data(), params.size() );
        return -1;
    }

    Channel *channel = _cm->BorrowChannel();
    if (channel == NULL) {
        BANYAN_LOG(LOG_ERROR, "no channel borrowed for request, %d:%s", _write_buf->out_size(), str_to_hex((const char *)_write_buf->out_pos(), (_write_buf->out_size() > 128 ? 128 : _write_buf->out_size() ) ).c_str() );
        return -2;
    }

    _request.statements.assign((const char *)_write_buf->out_pos(), _write_buf->out_size() );
    BANYAN_LOG(LOG_DEBUG, "client request, %d:%s", _write_buf->out_size(), str_to_hex((const char *)_write_buf->out_pos(), (_write_buf->out_size() > 128 ? 128 : _write_buf->out_size() ) ).c_str() );

	int size = _request.statements.length();
	channel->write_buf->require(size);
	memcpy(channel->write_buf->in_pos(), _request.statements.c_str(), size);
	channel->write_buf->commit(size);

	rc = channel->WriteRequest();
	if (rc != Client::WRITE_OK) {
	    channel->Reset();
        _cm->ReturnChannel(channel, 1, 1, true);
		BANYAN_LOG(LOG_WARN, "channel WriteRequest() error, %d:%s", _write_buf->out_size(), str_to_hex((const char*)_write_buf->out_pos(), (_write_buf->out_size() > 128 ? 128 : _write_buf->out_size() ) ).c_str() );
		return -3;
	}

	while (true) {
        rc = channel->ReadResponse();
        if (rc == Client::READ_AGAIN) {
            continue;
        } else if (rc == Client::READ_ERROR || rc == Client::READ_CLOSE || rc == Client::PARSE_ERROR) {
            channel->Reset();
            _cm->ReturnChannel(channel, 1, 1, true);
            BANYAN_LOG(LOG_WARN, "channel ReadResponse() error, %d:%s", _write_buf->out_size(), str_to_hex((const char*)_write_buf->out_pos(),(_write_buf->out_size() > 128 ? 128 : _write_buf->out_size() ) ).c_str() );
            return -4;
        } else {
            // PARSE_OK
            break;
        }
	}

    result.status = channel->res.items[0];
    if (channel->res.items.size() > 1) {
        result.value.assign(channel->res.items.begin() + 1, channel->res.items.end() );
    }
    BANYAN_LOG(LOG_DEBUG, "channel reponse:%s", channel->res.statements.c_str());

    if (channel->res.items[0] == BANYAN_RESPONSE_ERROR) {
        channel->Reset();
        _cm->ReturnChannel(channel, 1, 1, false);
		BANYAN_LOG(LOG_WARN, "channel receive error response, %d:%s", _write_buf->out_size(), str_to_hex((const char*)_write_buf->out_pos(), (_write_buf->out_size() > 128 ? 128 : _write_buf->out_size() ) ).c_str() );
        return -5;
    } else if (channel->res.items[0] == BANYAN_RESPONSE_BUFFER) {
        channel->Reset();
        _cm->ReturnChannel(channel, 1, 0, false);
		BANYAN_LOG(LOG_WARN, "channel receive buffered response, %d:%s", _write_buf->out_size(), str_to_hex((const char*)_write_buf->out_pos(), (_write_buf->out_size() > 128 ? 128 : _write_buf->out_size() ) ).c_str() );
        return -6;
    } 

    // not_found or ok
    channel->Reset();
    _cm->ReturnChannel(channel, 1, 0, false);
    return 0;
}

int BanyanClient::Set(BytesRef key, BytesRef value) {
    Result result;
    int res = Request("set", key, value, result);
    if (res != 0 || result.value.size() < 1) {
        BANYAN_LOG(LOG_WARN, "cmd:set, key:%s, failed:%d", key.data(), res);
        return -1;
    }

    return str_to_int32(result.value[0].data(), result.value[0].size());
}

int BanyanClient::Get(BytesRef key, std::string &value) {
    Result result;
    int res = Request("get", key, result);
    if (res != 0 || result.value.size() < 1) {
        BANYAN_LOG(LOG_WARN, "cmd:get, key:%s, failed:%d", key.data(), res);
        return -1;
    }

    value = result.value[0].ToString();
    return 1;
}


int BanyanClient::HSet(BytesRef key, BytesRef field, BytesRef value) {
    Result result;
    int res = Request("hset", key, field, value, result);
    if (res != 0 || result.value.size() < 1) {
        BANYAN_LOG(LOG_WARN, "cmd:hset, key:%s, field:%s, failed:%d", key.data(), field.data(), res);
        return -1;
    }

    return str_to_int32(result.value[0].data(), result.value[0].size());
}

int BanyanClient::HGet(BytesRef key, BytesRef field, std::string &value) {
    Result result;
    int res = Request("hget", key, field, result);
    if (res != 0 || result.value.size() < 1) {
        BANYAN_LOG(LOG_WARN, "cmd:hget, key:%s, field:%s, failed:%d", key.data(), field.data(), res);
        return -1;
    }

    value = result.value[0].ToString();
    return 1;
}

int BanyanClient::HScan(BytesRef key, BytesRef begin, BytesRef end, int limit, StringList_t &vals) {
    Result result;
    std::string limit_s = str_from_int32(limit);
    int res = Request("hscan", key, begin, end, limit_s, result);
    if (res != 0 || (result.value.size() % 2)) {
        BANYAN_LOG(LOG_WARN, "cmd:hscan, key:%s, begin:%s, end:%s, limit:%d, failed:%d", key.data(), begin.data(), end.data(), limit, res);
        return -1;
    }

    for (auto iter = result.value.begin(); iter != result.value.end(); iter++) {
        vals.push_back(iter->ToString() );
        iter++;
        vals.push_back(iter->ToString() );
    }

    return (vals.size() / 2);
}

int BanyanClient::HGetAll(BytesRef key, StringList_t &vals) {
    Result result;
    int res = Request("hgetall", key, result);
    if (res != 0 || (result.value.size() % 2)) {
        BANYAN_LOG(LOG_WARN, "cmd:hgetall, key:%s, failed:%d", key.data(), res);
        return -1;
    }

    for (auto iter = result.value.begin(); iter != result.value.end(); iter++) {
        vals.push_back(iter->ToString() );
        iter++;
        vals.push_back(iter->ToString() );
    }

    return (vals.size() / 2);
}

/*
std::string BanyanClient::Get(std::string key) {
    std::vector<std::string> args;
    args.push_back(key);
    std::string s = request("get", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return res[3];
}

int64_t BanyanClient::Set(std::string key, std::string val) {
    std::vector<std::string> args;
    args.push_back(key);
    args.push_back(val);
    std::string s = request("set", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_int64(res[3]);
}

int64_t BanyanClient::Setx(std::string key, std::string val, uint32_t ttl) {
    std::vector<std::string> args;
    args.push_back(key);
    args.push_back(val);
    args.push_back(str_from_uint32(ttl));
    std::string s = request("setx", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_int64(res[3]);
}
int64_t BanyanClient::Expire(std::string key, uint32_t ttl) {
    std::vector<std::string> args;
    args.push_back(key);
    args.push_back(str_from_uint32(ttl));
    std::string s = request("expire", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_int64(res[3]);
}
std::string BanyanClient::GetSet(std::string key, std::string val) {
    std::vector<std::string> args;
    args.push_back(key);
    args.push_back(val);
    std::string s = request("getset", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return res[3];
}
int64_t BanyanClient::Del(std::string key) {
    std::vector<std::string> args;
    args.push_back(key);
    std::string s = request("del", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_int64(res[3]);
}
int64_t BanyanClient::Incr(std::string key, int64_t n) {
    std::vector<std::string> args;
    args.push_back(key);
    args.push_back(str_from_int64(n));
    std::string s = request("incr", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_int64(res[3]);
}
int64_t BanyanClient::Exists(std::string key) {
    std::vector<std::string> args;
    args.push_back(key);
    std::string s = request("exists", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_int64(res[3]);
}
bool BanyanClient::GetBit(std::string key, uint32_t n) {
    std::vector<std::string> args;
    args.push_back(key);
    args.push_back(str_from_uint32(n));
    std::string s = request("getbit", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_uint32(res[3]);
}
bool BanyanClient::SetBit(std::string key, uint32_t n, bool flags) {
    std::vector<std::string> args;
    args.push_back(key);
    args.push_back(str_from_uint32(n));
    args.push_back(flags ? "true" : "false");
    std::string s = request("setbit", args);
    std::vector<std::string> res = str_split(s.c_str(), '\n');
    return str_to_uint32(res[3]);
}
std::vector<std::string> BanyanClient::Keys(std::string start, std::string end, uint32_t limit) {
    std::vector<std::string> args;
    args.push_back(start);
    args.push_back(end);
    args.push_back(str_from_uint32(limit));
    std::string s = request("keys", args);
    std::vector<std::string> tmp = str_split(s.c_str(), '\n');
    std::vector<std::string> res;
    for (int i=3; i<(int)tmp.size(); i += 2) {
        res.push_back(tmp[i]);
    }
    return res;
}
std::vector<std::string> BanyanClient::Rkeys(std::string start, std::string end, uint32_t limit) {
    std::vector<std::string> args;

    args.push_back(start);
    args.push_back(end);
    args.push_back(str_from_uint32(limit));
    std::string s = request("rkeys", args);
    std::vector<std::string> tmp = str_split(s.c_str(), '\n');
    std::vector<std::string> res;
    for (int i=3; i<(int)tmp.size(); i += 2) {
        res.push_back(tmp[i]);
    }
    return res;
}
*/

/*===========================================================================================
 * The old version of str_split(std::string &str, char split_char) has bug in std::string.c_str()     *
 * So the str_split_v2(std::string &str, std::string delim) appears.                                  *
 *==========================================================================================*/
/*
static std::vector<std::string> str_split_v2(std::string &str, std::string delim) {
    std::vector<std::string> res;
    if (delim.empty()) {
        return res;
    }
    size_t start = 0;
    size_t index = str.find_first_of(delim, 0);
    while (index != str.npos) {

        if (start != index) {
            res.push_back(str.substr(start, index-start));
        }
        start = index + 1;
        index = str.find_first_of(delim, start);
    }
    if (!str.substr(start).empty()) {
        res.push_back(str.substr(start));
    }
    return res;
}
*/
/*========================================================================================*/
/*
std::map<std::string, std::string> BanyanClient::Scan(std::string start, std::string end, uint32_t limit) {
    std::vector<std::string> args;
    args.push_back(start);
    args.push_back(end);
    args.push_back(str_from_uint32(limit));
    std::string s = request("scan", args);
    std::vector<std::string> tmp = str_split_v2(s, "\n");
    std::map<std::string, std::string> res;
    for (int i=3; i<(int)tmp.size();) {
        res[tmp[i]] = tmp[i+2];
        i += 4;
    }
    return res;
}
std::map<std::string, std::string> BanyanClient::MultiGet(std::vector<std::string> keys) {
    std::vector<std::string> args;
    args.insert(args.end(), keys.begin(), keys.end());
    std::string s = request("multi_get", args);
    std::vector<std::string> tmp = str_split_v2(s, "\n");
    std::map<std::string, std::string> res;
    for (int i=3; i<(int)tmp.size();) {
        res[tmp[i]] = tmp[i+2];
        i += 4;
    }
    return res;

}
int64_t BanyanClient::MultiSet(std::vector<std::string> kvs) {
    std::vector<std::string> args;
    args.insert(args.end(), kvs.begin(), kvs.end());
    std::string s = request("multi_set", args);
    std::vector<std::string> res = str_split_v2(s, "\n");
    return str_to_int64(res[3]);
}

DBFileKeys  BanyanClient::SetFile(const char* pathname) {
    int src = open(pathname, O_RDONLY);
    std::vector<std::string> keys;
    char buf[BUFSIZE];
    int i = 0;
    int nrd = 0;
    while (1) {
        nrd = read(src, buf, BUFSIZE);
        if (nrd <= 0) {
            break;
        }
        char key[BUFSIZE];
        std::cout<< std::string(buf, nrd) ;
        sprintf(key, "des_%d", i++);
        this->Set(key, std::string(buf, nrd) );
        keys.push_back(key);
    };
    close(src);
    return keys;
}
const char* BanyanClient::GetFile(DBFileKeys dbfilekeys) {
    const char* filename = "file_download_from_db.dat";
    int des = open(filename, O_RDWR | O_CREAT, S_IRUSR | S_IWUSR);
    for(auto iter = dbfilekeys.begin(); iter != dbfilekeys.end(); iter++) {
        std::string str = this->Get(*iter);
        write(des, str.c_str(), str.length());
    }
    close(des);

    return filename;
}

*/




}

#if BANYAN_ENABLE_TEST
#include <gtest/gtest.h>
#include <iostream>

TEST(test_cpp_api, api_test) {
    using namespace banyan;

    BanyanClient client;
    client.Init("10.10.105.5:18000", "in_device", "device");

    std::string res;
    //std::string key("k1");
    //std::string val("v1");
    client.Set("k1", "v1");
    client.Get("k1", res);
    fprintf(stdout, "res:%s", res.c_str());
}

#endif // BANYAN_ENABLE_TEST
