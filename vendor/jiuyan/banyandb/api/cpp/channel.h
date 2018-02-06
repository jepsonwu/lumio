#ifndef BANYAN_CPP_CHANNEL_H_
#define BANYAN_CPP_CHANNEL_H_

#include <common/banyan_common.h>
#include <unistd.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <errno.h>
#include <string>

namespace banyan {
;

class Channel : public Client {
public:
    Channel(int sock, std::string peer, uint32_t epoch, uint32_t group);
    ~Channel() {};

    /****************
     * check Channel status
     * return true if sock is closed, false is alive
     */
    bool IsDown();

    int FD() {
        return fd;
    }

    uint32_t GetEpoch();

    uint32_t GetGroupId();

private:

    uint32_t    _epoch;
    uint32_t    _group_id;
};

}

#endif // BANYAN_CPP_CHANNEL_H_
