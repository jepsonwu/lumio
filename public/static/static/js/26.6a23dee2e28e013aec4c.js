webpackJsonp([26],{HcKJ:function(e,a){},gAII:function(e,a,t){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var r=t("VsUZ"),l={data:function(){return{info:{bank:"",bankfiliale:"",bank_card:"",real_name:"",id_card:""},rules:{bank:[{required:!0,message:"请输入银行名称",trigger:"blur"}],bank_card:[{required:!0,message:"请输入银行卡号",trigger:"blur"}],real_name:[{required:!0,message:"请输入真实姓名",trigger:"blur"}],id_card:[{required:!0,message:"请输入身份证号",trigger:"blur"}]}}},methods:{onSubmit:function(e){var a=this;this.$refs[e].validate(function(e){e&&r.a.createFund(a.info).then(function(e){e.succ?a.$router.push("/backManage/bankcardlist"):(a.$message({showClose:!0,message:e.msg,type:"error"}),"20112"===e.code&&a.$router.push("/login"))},function(e){a.$message({showClose:!0,message:e,type:"error"})})})}}},n={render:function(){var e=this,a=e.$createElement,t=e._self._c||a;return t("el-form",{ref:"form",attrs:{model:e.info,rules:e.rules,"label-width":"80px"}},[t("p",{staticClass:"title"},[e._v("绑定提现账号")]),t("p"),e._v(" "),t("el-form-item",{staticClass:"le_width",attrs:{label:"银行名称",prop:"bank"}},[t("el-input",{attrs:{placeholder:"请输入内容"},model:{value:e.info.bank,callback:function(a){e.$set(e.info,"bank",a)},expression:"info.bank"}})],1),e._v(" "),t("el-form-item",{staticClass:"le_width",attrs:{label:"所属支行"}},[t("el-input",{attrs:{placeholder:"请输入内容"},model:{value:e.info.bankfiliale,callback:function(a){e.$set(e.info,"bankfiliale",a)},expression:"info.bankfiliale"}})],1),e._v(" "),t("el-form-item",{staticClass:"le_width",attrs:{label:"银行卡号",prop:"bank_card"}},[t("el-input",{attrs:{placeholder:"请输入内容"},model:{value:e.info.bank_card,callback:function(a){e.$set(e.info,"bank_card",a)},expression:"info.bank_card"}})],1),e._v(" "),t("el-form-item",{staticClass:"le_width",attrs:{label:"持卡人",prop:"real_name"}},[t("el-input",{attrs:{placeholder:"请输入内容"},model:{value:e.info.real_name,callback:function(a){e.$set(e.info,"real_name",a)},expression:"info.real_name"}})],1),e._v(" "),t("el-form-item",{staticClass:"le_width",attrs:{label:"身份证号",prop:"id_card"}},[t("el-input",{attrs:{placeholder:"请输入内容"},model:{value:e.info.id_card,callback:function(a){e.$set(e.info,"id_card",a)},expression:"info.id_card"}})],1),e._v(" "),t("el-form-item",[t("el-button",{attrs:{type:"primary"},on:{click:function(a){e.onSubmit("form")}}},[e._v("提交")])],1)],1)},staticRenderFns:[]};var i=t("VU/8")(l,n,!1,function(e){t("HcKJ")},"data-v-1b7062cd",null);a.default=i.exports}});
//# sourceMappingURL=26.6a23dee2e28e013aec4c.js.map