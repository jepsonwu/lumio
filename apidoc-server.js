/**
 * Created by xinghuo on 2017/8/4.
 */
const express = require('express');
const expressApiDoc = require('express-apidoc');
const app = express();

app.use('/', expressApiDoc({
    src: ["./modules"],
    // includeFilters: [".*\\.js$"] // like -f option
}));

app.listen(9000, function () {
    console.log('open http://127.0.0.1:9000')
});
