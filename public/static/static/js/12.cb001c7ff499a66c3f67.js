webpackJsonp([12],{Cai6:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=a("VsUZ"),s={data:function(){return{form:{username:"",gender:"",avatar:"",qq:"",email:"",open_status:"",taobao_account:"",jd_account:""}}},created:function(){this.getUserInfo()},methods:{getUserInfo:function(){var e=this;o.a.getmyinfo().then(function(t){t.succ?(e.form=t.data,e.form.open_status="1"===e.form.open_status):(e.$message({showClose:!0,message:t.msg,type:"error"}),"20122"===t.data.code&&e.$router.push("/login"))},function(t){e.$message({showClose:!0,message:t,type:"error"})})},beforeUpload:function(e){var t=this,a={extension:e.name.split(".")[1]};return o.a.getToken(a).then(function(a){if(a.succ){var s=new FormData;s.append("file",e),s.append("token",a.data.token),s.append("key",a.data.key),o.a.upload(s).then(function(e){t.form.avatar=a.data.host+e.key},function(e){t.$message({showClose:!0,message:e,type:"error"})})}else t.$message({showClose:!0,message:a.msg,type:"error"}),"20122"===a.data.code&&t.$router.push("/login")},function(e){t.$message({showClose:!0,message:e,type:"error"})}),!1},onSubmit:function(){var e=this;this.form.open_status=!0===this.form.open_status?1:0,o.a.setmyinfo(this.form).then(function(t){t.succ?(e.getUserInfo(),e.$message({showClose:!0,message:"修改成功",type:"success"})):e.$message({showClose:!0,message:t.msg,type:"error"})},function(t){e.$message({showClose:!0,message:t,type:"error"})})}}},r={render:function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("el-form",{ref:"form",attrs:{model:e.form,"label-width":"110px"}},[a("el-form-item",{staticClass:"inp_width",attrs:{label:"用户名称"}},[a("el-input",{model:{value:e.form.username,callback:function(t){e.$set(e.form,"username",t)},expression:"form.username"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"用户头像"}},[a("el-upload",{staticClass:"avatar-uploader",attrs:{action:"123","show-file-list":!1,"before-upload":e.beforeUpload}},[e.form.avatar?a("img",{staticClass:"avatar",attrs:{src:e.form.avatar}}):a("i",{staticClass:"el-icon-plus avatar-uploader-icon"})])],1),e._v(" "),a("el-form-item",{attrs:{label:"性别"}},[a("el-radio-group",{model:{value:e.form.gender,callback:function(t){e.$set(e.form,"gender",t)},expression:"form.gender"}},[a("el-radio",{attrs:{label:"0"}},[e._v("女")]),e._v(" "),a("el-radio",{attrs:{label:"1"}},[e._v("男")]),e._v(" "),a("el-radio",{attrs:{label:"2"}},[e._v("未知")])],1)],1),e._v(" "),a("el-form-item",{staticClass:"inp_width",attrs:{label:"QQ"}},[a("el-input",{model:{value:e.form.qq,callback:function(t){e.$set(e.form,"qq",t)},expression:"form.qq"}})],1),e._v(" "),a("el-form-item",{staticClass:"inp_width",attrs:{label:"Email"}},[a("el-input",{model:{value:e.form.email,callback:function(t){e.$set(e.form,"email",t)},expression:"form.email"}})],1),e._v(" "),a("el-form-item",{staticClass:"inp_width",attrs:{label:"淘宝任务账号"}},[a("el-input",{model:{value:e.form.taobao_account,callback:function(t){e.$set(e.form,"taobao_account",t)},expression:"form.taobao_account"}})],1),e._v(" "),a("el-form-item",{staticClass:"inp_width",attrs:{label:"京东任务账号"}},[a("el-input",{model:{value:e.form.jd_account,callback:function(t){e.$set(e.form,"jd_account",t)},expression:"form.jd_account"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"接收任务开关"}},[a("el-switch",{model:{value:e.form.open_status,callback:function(t){e.$set(e.form,"open_status",t)},expression:"form.open_status"}})],1),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary"},on:{click:e.onSubmit}},[e._v("确定")])],1)],1)},staticRenderFns:[]};var n=a("VU/8")(s,r,!1,function(e){a("pHSo")},"data-v-715cd879",null);t.default=n.exports},pHSo:function(e,t){}});
//# sourceMappingURL=12.cb001c7ff499a66c3f67.js.map