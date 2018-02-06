#ifndef BANYAN_CPP_API_H_
#define BANYAN_CPP_API_H_

#include <string>
#include <vector>
#include <common/banyan_common.h>
#include "channel_manager.h"

namespace banyan {

typedef std::vector<Bytes> BytesList_t; 

typedef std::vector<BytesRef> BytesRefList_t;

typedef std::vector<std::string> StringList_t;

typedef struct Result {
    Bytes status;
    BytesList_t value;
} Result;

class BanyanClient {
public:
    BanyanClient();
    ~BanyanClient();

    int Init(const std::string hosts, const std::string ns, const std::string table);

    void SetNamespace(const std::string ns);

    void SetTable(const std::string table);

    const std::string GetNamespace();

    const std::string GetTable();


    int Set(BytesRef key, BytesRef val);

    int Get(BytesRef key, std::string &val);

    int HSet(BytesRef key, BytesRef field, BytesRef val);

    int HGet(BytesRef key, BytesRef field, std::string &val);

    int HScan(BytesRef key, BytesRef begin, BytesRef end, int limit, StringList_t &vals);

    int HGetAll(BytesRef key, StringList_t &vals);
    
    /*
    std::string              Get(std::string key);
    int64_t             Set(std::string key, std::string val);

    DBFileKeys          SetFile(const char* pathname);
    const char*         GetFile(DBFileKeys dbfilekeys);
    int64_t             Setx(std::string key, std::string val, uint32_t ttl);
    int64_t             Expire(std::string key, uint32_t ttl);
    std::string              GetSet(std::string key, std::string val);
    int64_t             Del(std::string key);
    int64_t             Incr(std::string key, int64_t n);
    int64_t             Exists(std::string key);
    bool                GetBit(std::string key, uint32_t n);
    bool                SetBit(std::string key, uint32_t n, bool flags);
    std::vector<std::string>      Keys(std::string start, std::string end, uint32_t limit);
    std::vector<std::string>      Rkeys(std::string start, std::string end, uint32_t limit);
    std::map<std::string, std::string> Scan(std::string start, std::string end, uint32_t limit);
    std::map<std::string, std::string> Rscan(std::string start, std::string end, uint32_t limit);
    std::map<std::string, std::string> MultiGet(std::vector<std::string> keys);
    int64_t             MultiSet(std::vector<std::string> kvs);
    */

private:
    int Request(BytesRef cmd, Result &result);

    int Request(BytesRef cmd, BytesRef param, Result &result);

    int Request(BytesRef cmd, BytesRef param1, BytesRef param2, Result &result);

    int Request(BytesRef cmd, BytesRef param1, BytesRef param2, BytesRef param3, Result &result);

    int Request(BytesRef cmd, BytesRef param1, BytesRef param2, BytesRef param3, BytesRef param4, Result &result);

    int Request(BytesRefList_t &params, Result &result);

private:
    std::string         _ns;
    std::string         _table;
    std::string         _proto;

    ChannelManager*     _cm;
    BanyanRequest       _request;

    // this is not thread safe, should use lock in multi_threads
	BytesQueue 			*_write_buf;
    CacheAppendMempool  _pool;
};

}

#endif // BANYAN_CPP_API_H_
