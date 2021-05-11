  <?php
  ini_set('display_errors','off');
  error_reporting(E_ALL || ~ E_NOTICE);
  $action = $_GET['action'];
  $id = $_GET['id'];
  if(file_exists('install/install.lock')){
  include 'admin/config.php';
  $mdapi_interface = $sql_con->query("SELECT * FROM mdapi_interface WHERE id = {$id} ORDER BY CASE WHEN (istop = 1) THEN 0 ELSE 1 END ,TIME DESC");
  $isok = false;
  if ($mdapi_interface->num_rows > 0) {
    $api = $mdapi_interface->fetch_assoc();
    $isok = true;
  }
  if($action == 'interface' && $id != null && $id != ''){
    $titles = $api['name'];
    $layouts = 'assets/php/layout/interface.php';
  }else if($action == 'help'){
    $titles = '帮助';
    $layouts = 'assets/php/layout/help.php';
  }else if($action == 'about'){
    $titles = '关于';
    $layouts = 'assets/php/layout/about.php';
  }else if($action == 'setting' && $_SESSION['login'] == 1){
    $titles = '设置';
    $layouts = 'assets/php/layout/setting.php';
  }else if($action == 'support'){
    $titles = '赞助支持';
    $layouts = 'assets/php/layout/support.php';
  }else{
	$titles = $info['title'];
    $layouts = 'assets/php/layout/home.php';
    mdapiWebsite_visits($info['visit'],$info['visits']);
  };
   include 'assets/php/layout/header.php';
    $api_rankings = $sql_con->query("SELECT @rownum:=@rownum+1 AS ranking,id,name,transfers FROM (SELECT id,name,transfers FROM mdapi_interface ORDER BY transfers DESC) AS obj , (SELECT @rownum := 0) r LIMIT 5");
  if ($api_rankings->num_rows > 0) {
    $rankingss = array();
    while ($recordss = mysqli_fetch_array($api_rankings)) {
      $rankingss[] = $recordss;
    }
    }
     $api_ranking = $sql_con->query("SELECT id,name,transfer FROM mdapi_interface WHERE status!=1");
  if ($api_ranking->num_rows > 0) {
    $rankings = array();
    while ($records = mysqli_fetch_array($api_ranking)) {
      $rankings[] = $records;
    }
    }
	 $new_ranking = array();
  foreach($rankings as $row){
	  $number = json_decode($row['transfer'], true)[date('m-d')];
	  if(empty($number)) {
		 $number = 0;
	  }
	  $new_ranking[$row['name']] = $number;
  }
  arsort($new_ranking);
  $new_ranking = array_slice($new_ranking,0,5);
	$mdapi_feedbacks = $sql_con->query("SELECT COUNT(*) numbe FROM mdapi_feedback");
	if ($mdapi_feedbacks->num_rows > 0) {
    $mdapi_feedbacks = $mdapi_feedbacks->fetch_assoc();
    }
	$isapi_feedbacks = $sql_con->query("SELECT COUNT(*) numbe FROM mdapi_feedback WHERE apiid=".$api['id']);
	if ($isapi_feedbacks->num_rows > 0) {
    $isapi_feedbacks = $isapi_feedbacks->fetch_assoc();
    }
  ?>
  <div id="top-titlebar" class="mdui-appbar mdui-appbar-fixed mdui-color-white"> 
   <div class="mdui-toolbar mdui-color-white"> 
    <a href="javascript:toggle_drawer();" class="mdui-btn mdui-btn-icon mdui-ripple"><i class="mdui-icon material-icons">menu</i></a> 
    <a href="javascript:scrolTop('body_content');" class="mdui-typo-title"><?php echo $titles; ?></a> 
    <div class="mdui-toolbar-spacer"></div>
	<a class="mdui-btn mdui-btn-icon mdui-ripple" href="javascript:switch_Themes();"><i class="mdui-icon material-icons" id="home_theme_btn"><?php if($_COOKIE['theme'] == 'dark'){ echo 'brightness_7';}else{ echo 'brightness_4'; }?></i></a>
    <a class="mdui-btn mdui-btn-icon mdui-ripple" mdui-menu="{target:'#toolbar_menu'}"><i class="mdui-icon material-icons">more_vert</i></a> 
    <ul class="mdui-menu" id="toolbar_menu"> 
	 <?php if($action == 'interface'){ ?>
	  <?php if($_SESSION['login'] != 1){ ?>
     <li class="mdui-menu-item"><a href="javascript:api_admin_feed(<?php echo $api['id']; ?>,'<?php echo $api['name']; ?>');" class="mdui-ripple">反馈该接口</a></li> 
     <?php } ?>
	 <?php if($_SESSION['login'] == 1){ ?>
     <li class="mdui-menu-item"><a href="javascript:api_edit_dlg('<?php echo $api['id']; ?>',true).open();" class="mdui-ripple">编辑该接口</a></li>
	 <?php } ?>
	 <?php } ?>
	 <?php if(($action != 'setting' && $action != 'support') && $_SESSION['login'] != 1){ ?>
	 <li class="mdui-menu-item"><a href="javascript:locaUrl('?action=support');" class="mdui-ripple">赞助支持</a></li>
     <?php } ?>	 
	 <?php if($action == 'support'&& $_SESSION['login'] != 1){ ?>
	 <li class="mdui-menu-item"><a href="javascript:locaUrl('?action=about');" class="mdui-ripple">关于</a></li>
     <?php } ?>	 
	 <?php if((empty($action) || $action == 'interface' || $action == 'setting') && ($info['showadmin'] == 1 && $_SESSION['login'] != 1)){ ?>
	 <li class="mdui-menu-item"><a href="javascript:api_admin_login();" class="mdui-ripple">登录</a></li> 
	 <?php } ?>
	 <?php if($action == 'setting' && $_SESSION['login'] == 1){ ?>
	 <li class="mdui-menu-item"><a href="javascript:api_admin_logout();" class="mdui-ripple">退出登录</a></li> 
	 <?php } ?>
	 <?php if($action != 'interface' && $action != 'setting' && $_SESSION['login'] == 1){ ?>
	 <li class="mdui-menu-item"><a href="javascript:locaUrl('?action=setting');" class="mdui-ripple">设置</a></li> 
	 <?php } ?>
    </ul>  
   </div> 
  </div>
  <?php if($action == 'interface' && $id != null && $id != '' && $isok){ ?>
  <div class="mdui-text-color-white-text mdui-valign mdui-color-theme-accent" style="height: 150px;padding-top: 30px;">
<div class="mdui-center">
<p class="mdui-valign mdui-text-color-white"><font class="mdui-center" style="padding:0px 30px 0px 30px;font-size:15px"><?php echo $api['info']; ?></font></p><p class="mdui-valign mdui-text-color-white"><font class="mdui-center" style="padding:0px 30px 0px 30px"><font class="mdui-center" style="padding:0px 30px 0px 30px;font-size:10px"><i class="mdui-icon material-icons mdui-ripple" style="font-size:15px">equalizer</i>&nbsp<?php if($_SESSION['login'] == 1){ ?>日调用：<?php $isday_transfer_s = json_decode($api['transfer'], true)[date('m-d')]; if(!empty($isday_transfer_s)){ echo $isday_transfer_s; }else{ echo 0; } ?>&nbsp&nbsp&nbsp&nbsp<?php } ?>累计使用：<?php echo $api['transfers']; ?><?php if($_SESSION['login'] == 1){ ?>&nbsp&nbsp&nbsp&nbsp<i class="mdui-icon material-icons mdui-ripple" style="font-size:15px">feedback</i>&nbsp反馈数：<?php if(!empty($isapi_feedbacks['numbe'])){ echo $isapi_feedbacks['numbe']; }else{ echo 0; } } ?></font></font></p>
</div>
</div>
  <div class="mdui-tab mdui-tab-full-width mdui-color-theme-accent" mdui-tab>
   <a href="#api_document" class="mdui-ripple"><i class="mdui-icon material-icons">short_text</i>&nbsp接口文档</a> <a href="#api_status_code" class="mdui-ripple"><i class="mdui-icon material-icons">http</i>&nbsp状态码</a> <a href="#api_example" class="mdui-ripple"><i class="mdui-icon material-icons">code</i>&nbsp示例代码</a>
  </div>
  <?php } ?>
  <?php include 'assets/php/layout/drawer.php'; ?>
  <script type="text/javascript" src="//cdn.jsdelivr.net/npm/mdui@1.0.0/dist/js/mdui.min.js"></script> 
  <script type="text/javascript" src="//cdn.w3cbus.com/mdclub.org/static/docs/index.js"></script> 
  <script type="text/javascript" src="//cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
  <script type="text/javascript" src="//cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
  <script type="text/javascript" src="assets/js/0047ol.js"></script> 
  <div id="api_edit_dlg_<?php echo $api['id']; ?>" class="mc-user-edit mdui-dialog">
   <div class="mdui-dialog-title">
    <button class="mdui-btn mdui-btn-icon" style="margin-bottom:5px" mdui-dialog-close><i class="mdui-icon material-icons mdui-text-color-theme-icon">arrow_back</i></button>   编辑接口</div>
   <div class="mdui-dialog-content" style="padding:0 24px 0 24px">
   <div class="mdui-typo-subheading">接口信息</div>
   <div class="mdui-textfield mdui-textfield-floating-label">
     <label class="mdui-textfield-label">接口名称</label>
     <input id="api_edit_dlg_api_name_<?php echo $api['id']; ?>" class="mdui-textfield-input" type="text" value="<?php echo $api['name']; ?>" required/>
	 <div class="mdui-textfield-error">名称不能为空</div>
	 <div class="mdui-textfield-helper">请填写接口名称</div>
   </div>
   <div class="mdui-textfield mdui-textfield-floating-label">
     <label class="mdui-textfield-label">接口介绍</label>
     <textarea id="api_edit_dlg_api_info_<?php echo $api['id']; ?>" class="mdui-textfield-input" type="text" required><?php echo $api['info']; ?></textarea>
	 <div class="mdui-textfield-error">介绍不能为空</div>
	 <div class="mdui-textfield-helper">请填写接口介绍</div>
   </div>
   <div class="mdui-textfield mdui-textfield-floating-label">
     <label class="mdui-textfield-label">接口地址</label>
     <input id="api_edit_dlg_api_url_<?php echo $api['id']; ?>" class="mdui-textfield-input" type="text" value="<?php echo $api['url']; ?>" required/>
	 <div class="mdui-textfield-error">地址不能为空</div>
	 <div class="mdui-textfield-helper">请填写完整路径，包含http协议头</div>
   </div>
   <div class="mdui-textfield">
     <label class="mdui-textfield-label">示例参数</label>
     <input id="api_edit_dlg_api_rest_parm_<?php echo $api['id']; ?>" class="mdui-textfield-input" type="text" placeholder="（例：key1=value1&key2=value2）" value="<?php echo str_replace(array(';',','),array('&','='),$api['param']); ?>" onchange="api_edit_change_dlg_append(<?php echo $api['id']; ?>,$(this).val())" required/>
	 <div class="mdui-textfield-error">参数不能为空</div>
	 <div class="mdui-textfield-helper">格式：参数名=参数值&参数名=参数值（用于在接口文档展示）</div>
   </div>
   <div class="mdui-valign" style="width:100%;padding-top:14px;padding-bottom:14px">
     <font class="mdui-float-left" style="width:100%;padding-left:-2px">请求方式</font>
    <label class="mdui-checkbox mdui-float-right">
    <input id="api_edit_dlg_rest_type_get_<?php echo $api['id']; ?>" type="checkbox"<?php if($api['type'] == 0 || $api['type'] == 1){ echo ' checked'; } ?>/>
    <i class="mdui-checkbox-icon"></i><font style="margin-left:-12px;margin-right:10px">GET</font></label>
    <label class="mdui-checkbox mdui-float-right">
    <input id="api_edit_dlg_rest_type_post_<?php echo $api['id']; ?>" type="checkbox"<?php if($api['type'] == 0 || $api['type'] == 2){ echo ' checked'; }; if($api['retntype'] != 0 && $api['retntype'] != 1){ echo ' disabled'; } ?>/>
    <i class="mdui-checkbox-icon"></i><font style="margin-left:-12px">POST</font></label>
   </div>
   <div class="mdui-divider"></div><br/>
   <div class="mdui-typo-subheading">请求参数</div>
   <form id="api_edit_dlg_rest_parm_<?php echo $api['id']; ?>" name="result" onsubmit="return false">
   <?php
   $request = json_decode($api['request'], true);
   foreach ($request as $key => $value){
   ?>
   <div class="mdui-valign">
   <div class="mdui-textfield mdui-float-left" style="padding-right:10px;width:30%">
   <label class="mdui-textfield-label">参数</label>
   <input class="mdui-textfield-input" type="text" onchange="$('#rest_id_<?php echo $key; ?>_<?php echo $api['id']; ?>').attr('name',$(this).val());$('#rest_checkbox_<?php echo $key; ?>_<?php echo $api['id']; ?>').attr('name',$(this).val() + '_must')" value="<?php echo $key; ?>" required/>
   <div class="mdui-textfield-error">不能为空</div>
   </div>
   <div class="mdui-textfield mdui-float-left" style="padding-right:10px;width:100%">
   <label class="mdui-textfield-label">参数说明</label>
   <input id="rest_id_<?php echo $key; ?>_<?php echo $api['id']; ?>" name="<?php echo $key; ?>" class="mdui-textfield-input" type="text" value="<?php echo $request[$key]['details']['info']; ?>" required/>
   <div class="mdui-textfield-error">请填写参数的介绍</div>
   </div>
   <label class="mdui-checkbox mdui-float-right" style="width:10%">
   <input id="rest_checkbox_<?php echo $key; ?>_<?php echo $api['id']; ?>" name="<?php echo $key; ?>_must" type="checkbox"<?php $ifmust = $request[$key]['details']['must']; if($ifmust == 1){ echo ' checked'; }; ?>/>
   <i class="mdui-checkbox-icon"></i><font style="margin-left:-12px">必需</font>
   </label>
   <button class="mdui-btn mdui-btn-icon mdui-btn-dense" style="border-radius:100px;margin-left:5px;" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"><i class="mdui-icon material-icons">delete</i></button>
   </div>
   <?php } ?>
   </form>
   <button class="mdui-valign mdui-btn" onclick="api_edit_dlg_append(<?php echo $api['id']; ?>,0)"><i class="mdui-icon material-icons mdui-text-color-theme-icon-icon mdui-float-left">add</i>  添加</button>
   <?php if($api['retntype'] == 0){ ?><?php if(!empty($api['result']) && $api['result'] != '{}'){ ?><br/><div class="mdui-divider"></div>
   <br/><div class="mdui-typo-subheading">返回参数</div>
   <form id="api_edit_dlg_retn_parm_<?php echo $api['id']; ?>" name="request" onsubmit="return false">
   <?php
   $result = json_decode($api['result'], true);
   foreach ($result as $key => $value){
   ?>
   <div class="mdui-valign" style="overflow:visible">
   <div class="mdui-textfield mdui-float-left" style="padding-right:10px;width:30%">
   <label class="mdui-textfield-label">参数</label>
   <input class="mdui-textfield-input" type="text" onchange="$('#retn_id_<?php echo $key; ?>_<?php echo $api['id']; ?>').attr('name',$(this).val());$('#retn_select_<?php echo $key; ?>_<?php echo $api['id']; ?>').attr('name',$(this).val() + '_type')" value="<?php echo $key; ?>" required/>
   <div class="mdui-textfield-error">不能为空</div>
   </div>
   <div class="mdui-textfield mdui-float-left" style="padding-right:10px;width:100%">
   <label class="mdui-textfield-label">参数说明</label>
   <input id="retn_id_<?php echo $key; ?>_<?php echo $api['id']; ?>" name="<?php echo $key; ?>" class="mdui-textfield-input" type="text" value="<?php echo $result[$key]['details']['info']; ?>" required/>
   <div class="mdui-textfield-error">请填写参数的介绍</div>
   </div>
   <div class="mdui-textfield mdui-float-right" style="padding-bottom:28px;width:100px;overflow:visible">
   <label class="mdui-textfield-label">类型</label>
   <select class="mdui-select" id="retn_select_<?php echo $key; ?>_<?php echo $api['id']; ?>" name="<?php echo $key; ?>_type" mdui-select="{position:'bottom'}">
   <option value="string"<?php $iftype = $result[$key]['details']['type']; if($iftype == 'string'){ echo ' selected'; }; ?>>string</option>
   <option value="integer"<?php if($iftype == 'integer'){ echo ' selected'; }; ?>>integer</option>
   </select>
   </div>
   <button class="mdui-btn mdui-btn-icon mdui-btn-dense" style="border-radius:100px;margin-left:5px;" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"><i class="mdui-icon material-icons">delete</i></button>
   </div>
   <?php } ?>
   </form>
   <button class="mdui-valign mdui-btn" onclick="api_edit_dlg_append(<?php echo $api['id']; ?>,1)"><i class="mdui-icon material-icons mdui-text-color-theme-icon-icon mdui-float-left">add</i>  添加</button>
   <br/><div class="mdui-divider"></div><?php } ?>
   <?php if(!empty($api['result']) && $api['result'] != '{}'){ ?><br/><div class="mdui-typo-subheading">状态代码</div>
   <form id="api_edit_dlg_stau_code_<?php echo $api['id']; ?>" name="statuscode" onsubmit="return false">
   <?php
    $statuscode = json_decode($api['statuscode'], true);
    foreach ($statuscode as $key => $value){
   ?>
   <div class="mdui-valign" style="overflow:visible">
   <div class="mdui-textfield mdui-float-left" style="padding-right:10px;width:30%">
   <label class="mdui-textfield-label">状态码</label>
   <input class="mdui-textfield-input" type="text" onchange="$('#code_id_<?php echo $key; ?>_<?php echo $api['id']; ?>').attr('name',$(this).val());" value="<?php echo $key; ?>" required/>
   <div class="mdui-textfield-error">不能为空</div>
   </div>
   <div class="mdui-textfield mdui-float-left" style="width:100%">
   <label class="mdui-textfield-label">状态说明</label>
   <input id="code_id_<?php echo $key; ?>_<?php echo $api['id']; ?>" name="<?php echo $key; ?>" class="mdui-textfield-input" type="text" value="<?php echo $statuscode[$key]; ?>" required/>
   <div class="mdui-textfield-error">请填写状态码介绍</div>
   </div>
   <button class="mdui-btn mdui-btn-icon mdui-btn-dense" style="border-radius:100px;margin-left:5px;" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"><i class="mdui-icon material-icons">delete</i></button>
   </div>
   <?php } ?>
   </form>
   <button class="mdui-valign mdui-btn" onclick="api_edit_dlg_append(<?php echo $api['id']; ?>,2)"><i class="mdui-icon material-icons mdui-text-color-theme-icon-icon mdui-float-left">add</i>  添加</button><?php } ?><?php } ?>
   <br/>
   <button onclick="api_edit_dlg_save_btn(<?php echo $api['id']; ?>)" class="mdui-btn mdui-btn-block mdui-color-theme-accent mdui-ripple">保存</button>
   <br/><br/><br/>
  </div>
  </div>
  <?php include $layouts; ?>
    <div id="dlg_admin_feedback" class="mc-user-edit mdui-dialog" style="top:0px;min-width:100% !important;min-height:100% !important;border-radius:0 !important">
   <div class="mdui-dialog-title">
    <button class="mdui-btn mdui-btn-icon" style="margin-bottom:5px" mdui-dialog-close><i class="mdui-icon material-icons mdui-text-color-theme-icon">arrow_back</i></button>   接口反馈
   </div>
   <div class="mdui-dialog-content">
    <div class="mdui-table-fluid" style="border-radius:0 !important"> 
	      <table class="mdui-table mdui-table-hoverable mdui-table-selectable"> 
	       <thead>
	        <tr> 
	         <th>反馈时间</th> 
	         <th>反馈接口</th> 
			 <th>反馈内容</th>
			 <th>反馈者IP</th>
	        </tr>
	       </thead> 
	       <tbody>
		   <?php
		     $fedbacks = $sql_con->query("SELECT * FROM mdapi_feedback ORDER BY time DESC");
			 if ($fedbacks->num_rows > 0) {
				$fedback = array();
				while ($records = mysqli_fetch_array($fedbacks)) {
				   $fedback[] = $records;
				}
			 }
		        foreach ($fedback as $row) {
		        ?>
		        <tr id="fedbacks_id_<?php echo $row['id']; ?>"> 
		         <td style="white-space:pre-wrap"><?php echo date('Y-m-d H:i:s',$row['time']); ?></td> 
		         <td style="white-space:pre-wrap"><?php echo $row['apiname']; ?></td> 
				 <td style="white-space:pre-wrap"><?php echo $row['content']; ?></td> 
				 <td style="white-space:pre-wrap"><?php echo $row['ip']; ?></td> 
		        </tr> 
		        <?php } ?>
	       </tbody> 
	      </table> 
	     </div> 
		 <?php if($mdapi_feedbacks['numbe'] != 0){ ?>
		 <button class="mdui-fab mdui-fab-fixed mdui-ripple mdui-m-b-4 mdui-color-theme-accent" onclick="delete_feedback();"><i class="mdui-icon material-icons">delete</i></button>
		 <?php } ?>
  </div>
  </ul>
  </div>
  <?php
  include 'assets/php/layout/footer.php';
  if($_iserror){
	  echo "<script>mdui.alert('未正确安装或已损坏，请删除install文件夹下的install.lock文件后重新安装。','初始化失败',function(){locaUrl('./install/');},{confirmText:'确定',history:false,modal:true,closeOnEsc:false});</script>";
  }
  if($action == 'admin' && $_SESSION['login'] != 1){ ?>
  <script type="text/javascript">
  api_admin_login();
  </script>
  <?php
  }
  if ($mdapi_feedbacks['numbe'] != 0 && $_SESSION['login'] == 1) {
  ?>
  <script type="text/javascript">
  show_admin_feedback();
  </script>
  <?php
  }
  }else{
	  echo "<script>window.location.href = 'install/';</script>";
   }
  ?>