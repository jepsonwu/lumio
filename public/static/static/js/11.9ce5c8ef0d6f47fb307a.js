webpackJsonp([11],{MzjW:function(o,e,t){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=t("VsUZ"),s={data:function(){return{goodsList:[],goods_keywords:[],selectGood:"",form:{goods_id:"",task_name:"",goods_keyword:"",total_order_number:"",platform:""},rules:{goods_id:[{validator:function(o,e,t){""===e?t(new Error("请选择商品")):t()},trigger:"blur"}],task_name:[{validator:function(o,e,t){""===e?t(new Error("活动名称不能为空")):t()},trigger:"blur"}],goods_keyword:[{validator:function(o,e,t){""===e?t(new Error("搜索关键字不能为空")):t()},trigger:"blur"}],total_order_number:[{validator:function(o,e,t){""===e?t(new Error("活动份数不能为空")):t()},trigger:"blur"}],platform:[{validator:function(o,e,t){""===e?t(new Error("进店方式不能为空")):t()},trigger:"blur"}]}}},created:function(){this.getGoodsList()},methods:{getGoodsList:function(){var o=this;r.a.goodslist().then(function(e){e.succ?(console.log(e.data),o.goodsList=e.data.data):(o.$message({showClose:!0,message:e.msg,type:"error"}),"20112"===e.code&&o.$router.push("/login"))},function(e){o.$message({showClose:!0,message:e,type:"error"})})},changeGood:function(){this.goods_keywords=this.selectGood.goods_keywords.split("|"),this.form.goods_id=this.selectGood.id,console.log(this.form)},onSubmit:function(o){var e=this;this.$refs[o].validate(function(o){o&&r.a.createtask(e.form).then(function(o){o.succ?e.$router.push("/backManage/activitylist"):(e.$message({showClose:!0,message:o.msg,type:"error"}),"20112"===o.code&&e.$router.push("/login"))},function(o){e.$message({showClose:!0,message:o,type:"error"})})})}}},a={render:function(){var o=this,e=o.$createElement,t=o._self._c||e;return t("div",[t("p",{staticClass:"title"},[o._v("创建活动")]),t("p"),o._v(" "),t("el-form",{ref:"form",attrs:{model:o.form,rules:o.rules,"label-width":"160px"}},[t("el-form-item",{attrs:{label:"活动名称",prop:"task_name"}},[t("el-input",{model:{value:o.form.task_name,callback:function(e){o.$set(o.form,"task_name",e)},expression:"form.task_name"}})],1),o._v(" "),t("el-form-item",{attrs:{label:"活动商品",prop:"goods_id"}},[t("el-select",{attrs:{"value-key":"id",placeholder:"请选择"},on:{change:o.changeGood},model:{value:o.selectGood,callback:function(e){o.selectGood=e},expression:"selectGood"}},o._l(o.goodsList,function(o){return t("el-option",{key:o.id,attrs:{label:o.goods_name,value:o}})}))],1),o._v(" "),t("el-form-item",{attrs:{label:"搜索关键字",prop:"goods_keyword"}},[t("el-select",{attrs:{placeholder:"请选择"},model:{value:o.form.goods_keyword,callback:function(e){o.$set(o.form,"goods_keyword",e)},expression:"form.goods_keyword"}},o._l(o.goods_keywords,function(o,e){return t("el-option",{key:e,attrs:{label:o,value:o}})}))],1),o._v(" "),t("el-form-item",{attrs:{label:"进店方式",prop:"platform"}},[t("el-radio",{attrs:{label:"1"},model:{value:o.form.platform,callback:function(e){o.$set(o.form,"platform",e)},expression:"form.platform"}},[o._v("电脑")]),o._v(" "),t("el-radio",{attrs:{label:"2"},model:{value:o.form.platform,callback:function(e){o.$set(o.form,"platform",e)},expression:"form.platform"}},[o._v("手机")])],1),o._v(" "),t("el-form-item",{attrs:{label:"份数",prop:"total_order_number"}},[t("el-input",{model:{value:o.form.total_order_number,callback:function(e){o.$set(o.form,"total_order_number",e)},expression:"form.total_order_number"}})],1),o._v(" "),t("el-form-item",[t("el-button",{attrs:{type:"primary"},on:{click:function(e){o.onSubmit("form")}}},[o._v("立即创建")])],1)],1)],1)},staticRenderFns:[]};var l=t("VU/8")(s,a,!1,function(o){t("en9f")},"data-v-72750401",null);e.default=l.exports},en9f:function(o,e){}});
//# sourceMappingURL=11.9ce5c8ef0d6f47fb307a.js.map