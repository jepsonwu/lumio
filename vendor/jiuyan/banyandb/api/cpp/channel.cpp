#include "channel.h"

namespace banyan {
;

Channel::Channel(int sock, std::string peer, uint32_t epoch, uint32_t group_id) : Client(sock, peer) {
    _epoch = epoch;
    _group_id = group_id;

}

bool Channel::IsDown() {
    char buf[1];
    int sock = FD();
    bool is_down = false;

    int flags = fcntl(sock, F_GETFL);
    flags |= O_NONBLOCK;
    ::fcntl(sock, F_SETFL, flags);

    int n = ::recv(sock, buf, 1, MSG_PEEK);

    flags &= ~O_NONBLOCK;
    fcntl(sock, F_SETFL, flags);
    if (n == -1) {
        if (errno != EAGAIN && errno != EWOULDBLOCK && errno != EINTR) {
            is_down = true;
        }
    } else if (n == 0) {
        is_down = true;
    }

    return is_down;
}

uint32_t Channel::GetEpoch() {
    return _epoch;
}

uint32_t Channel::GetGroupId() {
    return _group_id;
}

}
