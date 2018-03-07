
struct RequestHeader {
    1: required string trace_id
    2: required string span_id
    3: optional string parent_span_id
    4: optional bool sampled // if true we should trace the request, if not set we have not decided.
    5: optional string seq_id
    6: optional i64 flags  // contains various flags such as debug mode on/off
    7: optional map<string, string> meta
}


service RequestTracerService {
    string  __jiuyan_service__header__v1__(1:RequestHeader header)
}

struct ResponseHeader {
    1: map<string, string> meta
}

/**
 * 实现握手，需要下面的结构体
 */
struct UpgradeReply {
    1:i32 version
}

struct UpgradeArgs {
    1: string app_id
    2: i32 version
}


