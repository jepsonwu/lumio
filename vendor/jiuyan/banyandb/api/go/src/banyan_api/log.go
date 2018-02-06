package banyan_api

import (
    "fmt"
    "os"
    "log"
)

const (
    LevelDebug = iota
    LevelInfo
    LevelError
    LevelFatal
)

var logger = log.New(os.Stderr, "", log.LstdFlags)

func LogInit(level int, filename string) {
    fp, err := os.OpenFile(filename, os.O_WRONLY|os.O_CREATE|os.O_APPEND, 0666)
    if err == nil {
        logger = log.New(fp, "", log.LstdFlags|log.Lshortfile)
    }
}

func Debug(s string, args ...interface{}) {
    s = "debug " + s
    logger.Output(2, fmt.Sprintf(s, args...))
}

func Info(s string, args ...interface{}) {
    s = "info " + s
    logger.Output(2, fmt.Sprintf(s, args...))
}

func Warn(s string, args ...interface{}) {
    s = "warn " + s
    logger.Output(2, fmt.Sprintf(s, args...))
}

func Error(s string, args ...interface{}) {
    s = "error " + s
    logger.Output(2, fmt.Sprintf(s, args...))
}

func Fatal(s string, args ...interface{}) {
    s = "fatal " + s
    logger.Output(2, fmt.Sprintf(s, args...))
}
