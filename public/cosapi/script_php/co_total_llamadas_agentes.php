<?php
session_start();
$base="corporativo_reportes";
$host="balanceo_basedatos_corporativo";
$user="corporativo_web";
$pass="corporativo_web";
$conn=mysql_connect($host,$user,$pass);
mysql_select_db($base,$conn);



////////////////MANAGER1////////////////////
ob_implicit_flush(false);
$username = "admin";
$secret   = "admin";
$socket = fsockopen("flotante_asterisk_corporativo","5038", $errornum, $errorstr);
$agents = array();
$curr_agent = "";
$better_status = array(	'AGENT_UNKNOWN' 	=> 'DESCONECTADO',
						'AGENT_IDLE' 		=> 'CONECTADO',
						'AGENT_ONCALL' 		=> 'HABLANDO',
						'AGENT_LOGGEDOFF' 	=> 'DESCONECTADO' );
$agente_status = array(	'LOGIN' 			=> 'CONECTADO',
						'CONECTADO' 		=> 'CONECTADO',
						'BACKOFFICE' 		=> 'BACKOFFICE',
						'LUNCH' 			=> 'REFRIGERIO',
						'REFRIGERIO' 		=> 'REFRIGERIO',
						'BREAK' 			=> 'BREAK',
						'RESUME' 			=> 'CONECTADO',
						'GESTORCORREO' 		=> 'GESTORCORREO',
						'GESTORPROVINCIA' 	=> 'GESTORPROVINCIA',
						'ONLINE' 			=> 'CONECTADO' );
if(!$socket)
{
	print "No se puede conectar al socket. Error #" . $errornum . ": " . $errorstr;
}
else
{
	fputs($socket, "Action: Login\r\n");
	fputs($socket, "UserName: ".$username."\r\n");
	fputs($socket, "Secret: ".$secret."\r\n\r\n");
	fputs($socket, "Action: Agents\r\n\r\n");
	fputs($socket, "Action: Logoff\r\n\r\n");
	$encoladas=0;
	while(!feof($socket)) {
		$info = fscanf($socket, "%s\t%s\r\n");
		switch($info[0]) {
			case "Agent:":
				$curr_agent = $info[1];
				$agents[$curr_agent] = array();
				$agents[$curr_agent]['Name'] = "";
				$agents[$curr_agent]['Status'] = "";
				$agents[$curr_agent]['LoggedInChan'] = "";
				$agents[$curr_agent]['LoggedInTime'] = "";
				$agents[$curr_agent]['TalkingTo'] = "";
				break;
			case "Name:":
				$agents[$curr_agent]['Name'] = $info[1];
				break;
			case "Status:":
				$agents[$curr_agent]['Status'] = $better_status[$info[1]];
				break;
			case "LoggedInChan:":
				$agents[$curr_agent]['LoggedInChan'] = $info[1];
				if(substr(trim($info[1]),0,6)=="Local/")
				{$encoladas++;}
				break;
			case "LoggedInTime:":
				if($info[1] != "0") {
					$agents[$curr_agent]['LoggedInTime'] = date("Y-m-d h:i:s", $info[1]);
				} else {
					$agents[$curr_agent]['LoggedInTime'] = "";
				}
				break;
			case "TalkingTo:":
				$agents[$curr_agent]['TalkingTo'] = $info[1];
				break;
			default:
				break;
		}
	}
	fclose($socket);
}
////////////////MANAGER1////////////////////

////////////////MANAGER2////////////////////
ob_implicit_flush(false);
$username = "admin";
$secret   = "admin";
$socket = fsockopen("flotante_asterisk_corporativo","5038", $errornum, $errorstr);
$chans = array();
$curr_chan = "";
if(!$socket)
{
	print "No se puede conectar al socket. Error #" . $errornum . ": " . $errorstr;
}
else
{
	fputs($socket, "Action: Login\r\n");
	fputs($socket, "UserName: ".$username."\r\n");
	fputs($socket, "Secret: ".$secret."\r\n\r\n");
	fputs($socket, "Action: Status\r\n\r\n");
	fputs($socket, "Action: Logoff\r\n\r\n");
	$encoladas=0;
	$i=0;$nchans=0;
	while(!feof($socket))
	{
		$info = fscanf($socket, "%s\t%s\r\n");
		switch($info[0])
		{
			case "Channel:":
				$i++;$nchans++;
				$curr_chan[$i] = $info[1];
				$chans[$curr_chan[$i]] = array();
				$chans[$curr_chan[$i]]['CallerID'] = "";
				$chans[$curr_chan[$i]]['CallerIDNum'] = "";
				$chans[$curr_chan[$i]]['CallerIDName'] = "";
				$chans[$curr_chan[$i]]['Account'] = "";
				$chans[$curr_chan[$i]]['State'] = "";
				$chans[$curr_chan[$i]]['Context'] = "";
				$chans[$curr_chan[$i]]['Extension'] = "";
				$chans[$curr_chan[$i]]['Priority'] = "";
				$chans[$curr_chan[$i]]['Seconds'] = "";
				$chans[$curr_chan[$i]]['Link'] = "";
				$chans[$curr_chan[$i]]['Uniqueid'] = "";
				break;
			case "CallerID:":
				$chans[$curr_chan[$i]]['CallerID'] = $info[1];
				break;
			case "CallerIDNum:":
				$chans[$curr_chan[$i]]['CallerIDNum'] = $info[1];
				break;
			case "CallerIDName:":
				$chans[$curr_chan[$i]]['CallerIDName'] = $info[1];
				break;
			case "Account:":
				$chans[$curr_chan[$i]]['Account'] = $info[1];
				break;
			case "State:":
				$chans[$curr_chan[$i]]['State'] = $info[1];
				break;
			case "Context:":
				$chans[$curr_chan[$i]]['Context'] = $info[1];
				break;
			case "Extension:":
				$chans[$curr_chan[$i]]['Extension'] = $info[1];
				break;
			case "Priority:":
				$chans[$curr_chan[$i]]['Priority'] = $info[1];
				break;
			case "Seconds:":
				$chans[$curr_chan[$i]]['Seconds'] = $info[1];
				break;
			case "Link:":
				$chans[$curr_chan[$i]]['Link'] = $info[1];
				break;
			case "Uniqueid:":
				$chans[$curr_chan[$i]]['Uniqueid'] = $info[1];
				break;
			default:
				break;
		}
	}
}
fclose($socket);
foreach( $chans as $chan=>$curr )
{

	if(substr($curr['Link'],0,6)=="Agent/")
	{
		$segundos[$curr['CallerIDNum']]=$curr['Seconds'];
	}
}
////////////////MANAGER2///////////////////

	$row=array();
	$total_agentes=0;
	$agentes_conectados=0;
	foreach( $agents as $agent=>$curr )
	{

	  $row["agente"]=$agent;
	  if(substr($curr['LoggedInChan'],0,4)=="SIP/")
	  {
	  	$row["anexo"]=substr($curr['LoggedInChan'],4,4);
		$total_agentes++;
	  }
	  //para resolver cola con asterisk--- revisar
	  elseif(substr($curr['LoggedInChan'],3,12)=="@atcliente-1")
	  {
	  	$row["anexo"]=substr($curr['LoggedInChan'],0,4);
		$total_agentes++;
	  }
	  elseif(substr($curr['LoggedInChan'],0,5)=="Local")
	  {
	  	$row["anexo"]=substr($curr['LoggedInChan'],6,4);
		$total_agentes++;
	  }


	    $usuario = $row["agente"];  // capturar el usuario del agente

		$agenterows=" SELECT * FROM CC_AGENTE WHERE CC_USUARIO='".$usuario."' ";
		$agenterowq=mysql_query($agenterows,$conn);
		$agenterow="";
		while($agenterowr=mysql_fetch_array($agenterowq))		{
			$NombreAgente   = $agenterowr["CC_NOMBRE"];
			$total_llamadas = $agenterowr["CC_TOTAL_LLAMADAS"];}
		mysql_free_result($agenterowq);

		//==================================== EDER JASVIR VELA ARMAS 14/08/216 ====================================
		$eventoactual  = " SELECT evento FROM corporativo_reportes.HD_ESTADOACTUAL";
		$eventoactual .= " WHERE DATE(fecha)=DATE(NOW()) AND agente='".$usuario."' ";
		$eventoactual .= " ORDER BY fecha DESC ";
		$eventoactual .= " LIMIT 1 ; ";

		$eventorowq = mysql_query($eventoactual,$conn);

		while($evento_actual=mysql_fetch_array($eventorowq))
		{
			if ($evento_actual["evento"]=="ONLINE"){
				$agentes_conectados ++;
			}
		}

		$total_llamadas=0;

		$total_llamadas_s  = " SELECT count(*) as total_llamadas from  corporativo_qstats.queue_stats_mv";
		$total_llamadas_s .= " where  DATE((DATETIME))=date(now())";
		$total_llamadas_s .= " and agent='Agent/".$row["agente"]."' ";
		$total_llamadas_s .= " and event in('COMPLETEAGENT','COMPLETECALLER','TRANSFER')";



		$actualizar_total_llamadas  = " UPDATE corporativo_reportes.CC_AGENTE ";
		$actualizar_total_llamadas .= " SET CC_TOTAL_LLAMADAS=(".$total_llamadas_s.") ";
		$actualizar_total_llamadas .= " WHERE CC_USUARIO='".$row["agente"]."'";



		$upd_total_llamadas = mysql_query($actualizar_total_llamadas, $conn);

		mysql_query($upd_total_llamadas,$conn);

		mysql_free_result($total_llamadas_q);

		mysql_free_result($agenterowq);

	}

	$upd_total_agentes="update corporativo_reportes.CC_CONSOLIDADO set CC_AGENTES='".$total_agentes."'  , CC_AGENTES_CONECTADOS='".$agentes_conectados."'";

	mysql_query($upd_total_agentes,$conn);

mysql_close($conn);	

?>
