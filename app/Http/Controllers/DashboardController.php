<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;
use Cosapi\Facades\phpAMI;
use Cosapi\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Cosapi\Models\User;
use Cosapi\Models\Anexo;
use Cosapi\Models\CallsQueueOnline;

use Cosapi\Http\Controllers\OutgoingCallsController;

class DashboardController extends CosapiController
{


  /**
   * [dashboard_01 Función que llama a la vista del Dashboard 1]
   * @return [view] [returna vista HTML del Dashboard 1]
   */
  public function dashboard_01()
  {
    return view('elements/dashboard/dashboard_01');
  }


  /**
   * [dashboard_02 Función que llama a la vista del Dashboard 2]
   * @return [view] [returna vista HTML del Dashboard 2]
  */
  public function dashboard_02(Request $request)
  {
    $ressultado_kpi = array();
    if($request->ajax()){
      $ressultado_kpi       = $this->list_kpi_dashboard_02('');
      return view('elements/dashboard/dashboard_02')->with($ressultado_kpi);
    }else{
      return view('elements/dashboard/dashboard_02')->with($ressultado_kpi);

    }
  }


  /**
    * [detail_kpi_dashboard_02 Función que carga el panel del KPI del Dashboard 2]
    * @param  Request $request   [Variable parar la recepción de datos enviados por POST]
    * @param  [String]  $evento  [Nombre del tipo de reporte a cargar]
    * @return [view]             [Vista con los datos del panel de KPI del Dashboard 2]
  */
  public function detail_kpi_dashboard_02(Request $request, $evento)
  {
    $ressultado_kpi       = $this->list_kpi_dashboard_02($evento);
    return view('elements/dashboard/panels_kpi/index')->with($ressultado_kpi);
  }

  /**
   * [total_encoladas Función que nos permite obtener el toal de llamadas encoladas]
   * @param  Request $request [Parametro enviado por POST]
   * @return [view]           [Returna vista con datos de llamadas encoladas]
   */
  public function total_encoladas(Request $request)
  {
    if($request->ajax()){

      $showEncoladas = $this->showEncoladas();

      return view('elements/dashboard/pictures_kpi/queue')->with(array(
                          'count_encoladas'           => count($showEncoladas)
                      ));
    }
  }

  /**
   * [detail_agents Carga el panel del detalle de estados en que sencuentran los agentes en el Dashboard 1]
   * @param  Request $request [Parametro enviado por POST]
   * @return [view]           [returna vista HTML del panel de detalle de agentes del Dashboard 1]
   */
  public function detail_agents(Request $request)
  {
    if($request->ajax()){

      // Obtener la lista de analistas conectados
      $list_agent_details = $this->list_agent_details();

      //Obtener Información de las llamadas entrantes del Asterisk sgún el anexo del analista
      $InformationCalls = $this->builderInformationCalls($this->showChannels());

      // Obtener el orden de los eventos
      // A futuro deberia obtenerse de la BD
      $order_events     = $this->order_events();
      $queueStatus      = $this->queueStatus();

      foreach ($list_agent_details as $agent_details) {
        // Agregar Columna para controlar el orden del evento
        $agent_details->order_events      =  $order_events[$agent_details->id_evento];

        // Cruzar informacion de los agentes con la informacion de las llamadas del asterisk
        if(isset($InformationCalls[$agent_details->anexo]['name_queue'])){
          $agent_details->name_queue      = $InformationCalls[$agent_details->anexo]['name_queue'];
          $agent_details->time_call       = $InformationCalls[$agent_details->anexo]['time_call'];
          $agent_details->number_origin   = $InformationCalls[$agent_details->anexo]['number_origin'];
        }else{
          $agent_details->name_queue      = '';
          $agent_details->time_call       = '';
          $agent_details->number_origin   = '';
        }

        // Estilizar colores de acuerdo a las llamadas entrantes
        $agent_details = $this->color_status($agent_details);
        if(isset($queueStatus['Agent/'.$agent_details->username])){
          $agent_details ->status_anexo_color = $queueStatus['Agent/'.$agent_details->username]['color_status'];
          $agent_details ->status_anexo_icon  = $queueStatus['Agent/'.$agent_details->username]['icon_status'];
        }else{
          $agent_details ->status_anexo_color = 'red';
          $agent_details ->status_anexo_icon  = 'fa fa-thumbs-down';
        }

      }

      $list_agent_details = $this->Ordernar_array($list_agent_details);

      return view('elements/dashboard/tables/table_detail_calls')->with(array(
                          'list_agent_details'           => $list_agent_details
                      ));
    }
  }


  /**
   * [detail_kpi Función que carga la vista de los KPI del Dahsboard 1]
   * @param  Request $request  [Parametro enviado por POST]
   * @return [array]           [returna vista HTML del panel de KPI del Dashboard 1]
   */
  public function detail_kpi(Request $request )
  {
        if($request->ajax()){
            $list_kpi_details     = $this->list_kpi_details();
            $Received             = $list_kpi_details->total_calls + $list_kpi_details->total_transfer + $list_kpi_details->total_abandoned;
            $Inbound              = $list_kpi_details->total_calls + $list_kpi_details->total_transfer;
            $Abandon              = $list_kpi_details->total_abandoned;
            $Transfer             = $list_kpi_details->total_transfer;
            $Answered_less_20     = $list_kpi_details->answ_20s;
            $Answered_less_15     = $list_kpi_details->answ_15s;
            $Answered_less_10     = $list_kpi_details->answ_10s;
            $Unaswered_less_20    = $list_kpi_details->abandon_20s;
            $Unaswered_less_15    = $list_kpi_details->abandon_15s;
            $Unaswered_less_10    = $list_kpi_details->abandon_10s;
            $Unaswered_high_20    = $list_kpi_details->total_abandoned - $list_kpi_details->abandon_20s;
            $Unaswered_high_15    = $list_kpi_details->total_abandoned - $list_kpi_details->abandon_15s;
            $Unaswered_high_10    = $list_kpi_details->total_abandoned - $list_kpi_details->abandon_10s;
            $Queue                = 0;
            $Sla_20               = convertDecimales(($list_kpi_details->answ_20s / $Received)*100,2);
            $Sla_15               = convertDecimales(($list_kpi_details->answ_15s / $Received)*100,2);
            $Sla_10               = convertDecimales(($list_kpi_details->answ_10s / $Received)*100,2);

            return view('elements/dashboard/pictures_kpi/index')->with(array(
                           'Inbound'              => $Inbound,
                           'Abandon'              => $Abandon,
                           'Transfer'             => $Transfer,
                           'Answered_less_20'          => $Answered_less_20,
                           'Answered_less_15'          => $Answered_less_15,
                           'Answered_less_10'          => $Answered_less_10,
                           'Unaswered_less_20'   => $Unaswered_less_20,
                           'Unaswered_less_15'   => $Unaswered_less_15,
                           'Unaswered_less_10'   => $Unaswered_less_10,
                           'Unaswered_high_20'   => $Unaswered_high_20,
                           'Unaswered_high_15'   => $Unaswered_high_15,
                           'Unaswered_high_10'   => $Unaswered_high_10,
                           'Queue'                => $Queue,
                           'Sla_20'               => $Sla_20,
                           'Sla_15'               => $Sla_15,
                           'Sla_10'               => $Sla_10,
                        ));
        }
  }


  /**
   * [detail_encoladas Función que lista las llamadas encoladas]
   * @param  Request $request [variable para la recepción de datos enviados por POST]
   * @return [view]           [Vista con datos de la llamada encolada]
   */
  public function detail_encoladas(Request $request)
  {
    if($request->ajax()){

      $showEncoladas = $this->showEncoladas();

      return view('elements/dashboard/tables/table_detail_encoladas')->with(array(
                          'showEncoladas'           => $showEncoladas
                      ));
    }
  }


  /**
   * [list_agent_details Función que retorna datos con respecto al detalle de los agentes del Dashboard 1]
   * @return [view] [Resultado de la consultado obtenido del Proccedure]
   */
    protected function list_agent_details(){
      $list_agent_details = \DB::select('call sp_detalle_agentes_dashboard()');
      return $list_agent_details;
    }


  /**
   * [list_kpi_details Función que retorna los KPI del Dashboard 1]
   * @return [view] [Resultado de la consultado obtenido del Proccedure]
   */
  protected function list_kpi_details(){
    $list_kpi_details = CallsQueueOnline::first();
    return $list_kpi_details;
  }


  /**
   * [order_events Función que retorna el orden de los agente en el detalle eventos del Dahboar 1]
   * @return [array] [Orden de los eventos para el detalle evenetos en el Dahboar 1.]
   */
  protected function order_events(){
    $orden_status = array(
      '1'   => '3',
      '2'   => '5',
      '3'   => '7',
      '4'   => '6',
      '5'   => '8',
      '6'   => '9',
      '7'   => '4',
      '8'   => '1',
      '9'   => '2',
      '10'  => '',
      '11'  => '',
      '12'  => '',
      '13'  => '',
      '14'  => '',
      '15'  => '10'
    );

    return $orden_status;
  }


  /**
   * [Ordernar_array Función para ordenar de manera personalizada los datos según los estados]
   * @param [array] $list_agent_details [Resultados ordenados personazadamente por os eventos]
   */
  protected function Ordernar_array($list_agent_details){

      // Obtener una lista de columnas
      foreach ($list_agent_details as $key => $row) {
        $OrdenStatus[$key]    = $row->order_events;
        $TotalLlamadas[$key]  = $row->totalcalls;
      }

      // Ordenar los datos con volumen descendiente, edicion ascendiente
      // Agregar $datos como el último parámetro, para ordenar por la llave común
      array_multisort($OrdenStatus, SORT_ASC, $TotalLlamadas, SORT_DESC, $list_agent_details);

      return $list_agent_details;
  }


  /**
   * [color_status Función que coloca los iconos segun el estado en que se encuentre el agente]
   * @param  [array] $agent_details [Datos de los estados del detalle de agente del monitor de colas]
   * @return [array]                [Inconos agregados e el detalle de agente]
   */
  protected function color_status($agent_details){
    $agent_details->icon  = '';
    $agent_details->color = '';
    $agent_details->type  = '';
    $agent_details->coloricon = '';

    switch ($agent_details->nombre_ultimoevento) {
      case 'Inbound':
        $agent_details->icon      = 'fa fa-phone';
        $agent_details->color     = 'green';
        $agent_details->type      = 'success';
        $agent_details->coloricon = 'green';
        break;
      case 'ACD':
        $agent_details->icon      = 'fa fa-phone';
        $agent_details->color     = 'blue';
        $agent_details->type      = 'info';
        $agent_details->coloricon = 'blue';
        break;
      case 'Refrigerio':
        $agent_details->icon      = 'fa fa-cutlery';
        $agent_details->color     = 'purple';
        $agent_details->type      = 'success';
        $agent_details->coloricon = 'purple';
        break;
      case 'SS.HH':
        $agent_details->icon      = 'fa fa-asterisk';
        $agent_details->color     = 'Purple';
        $agent_details->type      = 'danger';
        $agent_details->coloricon = 'purple';
        break;
      case 'OutBound':
        $agent_details->icon      = 'fa fa-phone';
        $agent_details->color     = 'orange';
        $agent_details->type      = 'warning';
        $agent_details->coloricon = 'yellow';
        break;
      case 'Break':
        $agent_details->icon      = 'fa fa-asterisk';
        $agent_details->color     = 'Purple';
        $agent_details->type      = 'danger';
        $agent_details->coloricon = 'Purple';
        break;
      case 'Gestión BackOffice':
        $agent_details->icon      = 'fa  fa-suitcase';
        $agent_details->color     = 'Purple';
        $agent_details->type      = 'primary';
        $agent_details->coloricon = 'Purple';
        break;
      case 'Capacitación':
        $agent_details->icon      = 'fa  fa-book';
        $agent_details->color     = 'Purple';
        $agent_details->type      = 'success';
        $agent_details->coloricon = 'Purple';
        break;
    }

    return $agent_details;

  }


  /**
   * [showChannels Función que pemite obtener el estago del agente en una cola]
   * @return [array] [Array con los datos obtenidos del asterisk]
   */
  protected function showChannels(){
    $errno    = "";
    $errstr   = "";
    $timeout  = "30";
    $host     = "10.200.74.253";
    $socket = fsockopen($host,"5038", $errno, $errstr,  $timeout);
    fputs($socket, "Action: Login\r\n");
    fputs($socket, "UserName: alf1712\r\n");
    fputs($socket, "Secret: alf1712\r\n\r\n");
    fputs($socket, "Action: command\r\n");
    fputs($socket, "command: Show Channels Verbose\r\n\r\n");
    fputs($socket, "Action: Logoff\r\n\r\n");

    $count    = 0;
    $array;
    $enlinea  = 0;
    while (!feof($socket)) {
      $wrets = fgets($socket, 8192);
      $token = strtok($wrets,' ');
      $j=0;

      while($token!=false & $count>=1){
        $array[$count][$j]=$token;
        $j++;
        $token = strtok(' ');
      }

      $count++;
      $wrets .= '<br>';
    }
    fclose($socket);

    return $array;
  }

  /**
   * [builderInformationCalls Función  que carga los datos de los agentes durante una llamada (Cola, Timepo Hablando, numero origen)]
   * @param  [type] $showChannels [Array con los datos devueltos del Asterisk de los agentes en llamadas]
   * @return [type]               [Array con la información tratada del Asterisk]
   */
  protected function builderInformationCalls($showChannels){
    $tamano_anexo = 3;
    $InformationCalls = [];

    foreach ($showChannels as $showChannel) {

      if(isset($showChannel[7]) && (isset($showChannel[3]) || isset($showChannel[4]))){
        $InformationCalls[$showChannel[7]]['time_call']  = '-';
        $InformationCalls[$showChannel[7]]['anexo']      = '-';

        if(strlen($showChannel[7]) == $tamano_anexo && ($showChannel[3] == 'Up' || $showChannel[4] == 'Up' ) ){
          $InformationCalls[$showChannel[7]]['anexo']      = $showChannel[7];
          $InformationCalls[$showChannel[7]]['time_call']  = $showChannel[8];
        }
      }

      //Para identificar llamadas entrantes
      if(isset($showChannel[9])){
        //Anexo obtenido de la lista del "Show channel verbose"
        $anexo_sip = substr($showChannel[9], 4,$tamano_anexo);

        if($showChannel[5] == 'Queue'){
          $InformationCalls[$anexo_sip]['number_origin']      = $showChannel[7];
          $InformationCalls[$anexo_sip]['name_queue']         = $showChannel[2];
        }
      }


      //Para identificar llamadas salientes
      if(isset($showChannel[7])){
        //Anexo obtenido de la lista del "Show channel verbose"

        if(strlen($showChannel[7]) == $tamano_anexo && $showChannel[5] == 'Dial' ){
          $InformationCalls[$showChannel[7]]['number_origin']      = $showChannel[2];
          $InformationCalls[$showChannel[7]]['name_queue']         = '-';
        }
      }

    }

    return $InformationCalls;
  }


  /**
   * [desconectar_agente Función que permite desconectar al agente]
   * @param  Request $request  [Variable que nos permite recibir datos por POST]
   * @param  [int]     $anexo    [Anexo el cual se piensa liberar]
   * @param  [string]  $username [Usuario del agente]
   * @return [array]             [Array con resultados del proceso de desconexión]
   */
  public function desconectar_agente(Request $request, $anexo , $username)
  {
      if($request->ajax()){
          $UserAgente         = $username;
          $NumeroAnexo        = $anexo;
          $Usuarios           = User::select()->where('username','=',$UserAgente)->get()->toArray();
          $Anexos             = Anexo::select()->where('name','=',$NumeroAnexo)->get()->toArray();
          $ultimo_evento_id   = $this->get_ultimo_evento($Usuarios[0]['id']);

          if ($this->conexion_ami()==true){
              $agentLogoff  = phpAMI::agentLogoff($UserAgente,'True');
              $this->desconexion_ami();

              if($agentLogoff['Response']=='Success'){
                /** Guardar Eventos  */
                $this->registrar_eventos('15',$Usuarios[0]['id'], $NumeroAnexo);

                /** Liberar Anexo  */
                $Anexos = Anexo::find($Anexos[0]['id']);
                $Anexos->user_id = Null;
                $Anexos->save();

                echo 'Success - Agent/'.$UserAgente .' Desconectado Correctamente ';
              }else{
                echo 'Error - Problemas al desconectar Agent/'.$this->UserAgente;
              } 
          }
          else{
            echo 'Error - Agent/'.$UserAgente.' se encuentra en una llamada';
          }
      }
  }


  /**
   * [queueStatus Función para ver el estado en el que se encuentra el agente en cada cola.]
   * @return [array] [Array con los iconos segun el estado en que el agente se encuentra en el asterisk,]
   */
  protected function queueStatus()
  {
    $color_status = '';
    $icon_status  = '';
    $statusqueue  = [];
    if ($this->conexion_ami()==true){

        $queueStatus       = phpAMI::queueStatus('', '');
        $this->desconexion_ami();
        $color = 'white';

        if ($queueStatus['Response'] == 'Success') {
          foreach ($queueStatus['list'] as $skill_sip) {
            foreach ($skill_sip['members'] as $members) {       
              $Status = $members['Status'];
              $Paused = $members['Paused'];
              //PAUSADO (SSHH,BREAK OTHERS EVENT)
              if($Status == '1' && $Paused == '1'){
                $color_status = 'red' ;
                $icon_status  = 'fa fa-thumbs-down' ;
              }

              //ACD
              if($Status == '1' && $Paused == '0'){
                $color_status = 'blue' ;
                $icon_status  = 'fa fa-thumbs-up' ;
              }

              //LLAMADA
              if($Status == '3' && $Paused == '0'){
                $color_status = 'green' ;
                $icon_status  = 'fa fa-thumbs-up' ;
              }
              $statusqueue[$members['Name']]['color_status'] = $color_status;
              $statusqueue[$members['Name']]['icon_status']  = $icon_status;
            }
            break;
          }
          return  $statusqueue;

        } else {
          return '<strong>¡Error! </strong> Al listar llamadas Activas del Asterisk';
        }
    }
  }


  //Obteniendos los datos para los indicadores del Dashboard 2
  /**
   * [list_kpi_dashboard_02 Función que obtiene los datos mostrados en el panel del KPI del Dashboard 2 ]
   * @param  [type] $evento [Nombre del tipo de reporte a cargar]
   * @return [type]         [Vista con los datos del panel de KPI del Dashboard 2]
   */
  protected function list_kpi_dashboard_02($evento )
  {
    $day_inicio = date('Y-m-d');
    $day_fin    = date('Y-m-d');

    switch ($evento) {
      case 'week':
        $dayweek    = dayweek();
        $day_inicio = $dayweek[0];
        $day_fin    = $dayweek[1];
        break;

      case 'month':
        $daymonth   = daymonth();
        $day_inicio = $daymonth[0];
        $day_fin    = $daymonth[1];
        break;
    }
    
    $list_kpi_dashboard_02   = \DB::select('CALL sp_data_kpi_dashboard("'.$day_inicio.'","'.$day_fin.'");');

    $Recibidas               = $list_kpi_dashboard_02[0]->Abandon + $list_kpi_dashboard_02[0]->Transfer + $list_kpi_dashboard_02[0]->Inbound;
    $Atendidas_porcentaje    = 98;
    $Abandonadas_porcentaje  = 95;
    $Transferidas            = $list_kpi_dashboard_02[0]->Transfer;
    $Abandonadas             = $list_kpi_dashboard_02[0]->Abandon;
    $Salientes               = $list_kpi_dashboard_02[0]->Outbound;
    $Incoming                = $list_kpi_dashboard_02[0]->Inbound;
    $Sla                     = $list_kpi_dashboard_02[0]->SLA;


    $ressultado_kpi  = array(
                           'Salientes'                => $Salientes,
                           'Transferidas'             => $Transferidas,
                           'Incoming'                 => $Incoming,
                           'Abandonadas'              => $Abandonadas,
                           'Atendidas_porcentaje'     => $Atendidas_porcentaje,
                           'Abandonadas_porcentaje'   => $Abandonadas_porcentaje,
                           'Sla'                      => $Sla
                        );

    return $ressultado_kpi;
  }


  protected function showEncoladas(){
    ob_implicit_flush(false);
    $socket     = fsockopen("10.200.74.253","5038", $errornum, $errorstr);
    $chans      = array();
    $curr_chan  = "";

    fputs($socket, "Action: Login\r\n");
    fputs($socket, "UserName: alf1712\r\n");
    fputs($socket, "Secret: alf1712\r\n\r\n");
    fputs($socket, "Action: Status\r\n\r\n");
    fputs($socket, "Action: Logoff\r\n\r\n");
    while(!feof($socket))
    {
      $info = fscanf($socket, "%s\t%s\r\n");
      switch($info[0])
      {
        case "Channel:":
          $curr_chan = $info[1];
          $chans[$curr_chan] = array();
          $chans[$curr_chan]['Context']     = "";
          $chans[$curr_chan]['CallerIDNum']   = "";
          $chans[$curr_chan]['CallerIDName']  = "";
          $chans[$curr_chan]['Seconds']     = "";
          $chans[$curr_chan]['Extension']   = "";
          $chans[$curr_chan]['Link']      = "";

          break;
        case "Context:"       : $chans[$curr_chan]['Context']       = $info[1];   break;
        case "CallerIDNum:"   : $chans[$curr_chan]['CallerIDNum']   = $info[1];   break;
        case "CallerIDName:"  : $chans[$curr_chan]['CallerIDName']  = $info[1];   break;
        case "Seconds:"       : $chans[$curr_chan]['Seconds']       = $info[1];   break;
        case "Extension:"     : $chans[$curr_chan]['Extension']     = $info[1];   break;
        case "Link:"          : $chans[$curr_chan]['Link']          = $info[1];   break;
        default         : break;
      }
    }
    fclose($socket);

    $a = 0;
    $encoladas = array();
    foreach( $chans as $chan => $curr )
    { 
      // El valor del Context cambia según el proyecto en el cual se instale el software
      if($curr['Context']=="context-claroempresas")
      {
        if ($curr['Link'] == '') {
          $encoladas[$a]['Contexto']     = $curr['Context'];
          $encoladas[$a]['CallerIDNum']  = $curr['CallerIDNum'];
          $encoladas[$a]['CallerIDName'] = $curr['CallerIDName'];
          $encoladas[$a]['Seconds']      = $curr['Seconds'];
          $encoladas[$a]['Queue']        = $curr['Extension'];
        }
      }
      $a++;
    }

    $TotalEncolados = array();
    foreach ($encoladas as $key => $row) {
      $TotalEncolados[$key]   = $row['Seconds'];
    }

    // Ordenar el array por tiempo.
    array_multisort($TotalEncolados, SORT_DESC, $encoladas);

    return $encoladas;
  }
}