webpackJsonp([25],{V1Uk:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=a("VsUZ"),r={data:function(){return{currentPage:1,total:0,tableData:[]}},created:function(){this.getList()},methods:{getList:function(){var t=this,e={store_id:this.storeid,goods_name:this.goods_name,page:this.currentPage};n.a.fundrecordlist(e).then(function(e){e.succ?(t.tableData=e.data.data,t.currentPage=parseInt(e.data.current_page),t.total=parseInt(e.data.total)):(t.$message({showClose:!0,message:e.msg,type:"error"}),"20112"===e.code&&t.$router.push("/login"))},function(e){t.$message({showClose:!0,message:e,type:"error"})})},handleCurrentChange:function(t){this.currentPage=t,this.getList()}}},s={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("el-table",{staticStyle:{width:"100%"},attrs:{data:t.tableData,stripe:""}},[a("el-table-column",{attrs:{prop:"id",label:"id",width:"80"}}),t._v(" "),a("el-table-column",{attrs:{label:"总金额"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.amount/100)+"元")])]}}])}),t._v(" "),a("el-table-column",{attrs:{label:"实际金额"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.actual_amount/100)+"元")])]}}])}),t._v(" "),a("el-table-column",{attrs:{label:"佣金"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("span",[t._v(t._s(e.row.commission/100)+"元")])]}}])}),t._v(" "),a("el-table-column",{attrs:{prop:"remarks",label:"备注"}})],1),t._v(" "),a("el-pagination",{staticStyle:{float:"right","margin-top":"10px"},attrs:{"current-page":t.currentPage,"page-size":10,layout:"total, prev, pager, next",total:t.total},on:{"current-change":t.handleCurrentChange,"update:currentPage":function(e){t.currentPage=e}}})],1)},staticRenderFns:[]};var o=a("VU/8")(r,s,!1,function(t){a("YoJn")},"data-v-1e66e770",null);e.default=o.exports},YoJn:function(t,e){}});
//# sourceMappingURL=25.42af6d83bc57160cb138.js.map