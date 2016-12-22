<?php

namespace Cosapi\Http\Controllers;

use Illuminate\Http\Request;
use Cosapi\Collector\Collector;

use Cosapi\Http\Requests;
use Cosapi\Http\Controllers\CosapiController;
use Cosapi\Models\Survey;

class SurveysController extends CosapiController
{
    /**
     * [index Función que trae la vista o datos del reporte de Surveys]
     * @param  Request $request [Variable que recibe datos enviados por POT]
     * @return [array]           [Retorna vista o array de datos del reporte de Surveys]
     */
    public function index(Request $request)
    {             
        if ($request->ajax()){
            if ($request->fecha_evento){
                return $this->list_surveys($request->fecha_evento);
            }else{
                return view('elements/surveys/index');
            }
        }
    }

    /**
     * [list_surveys Función para obtener los datos a cargar en el repote Surveys]
     * @param  [string] $fecha_evento [Fecha a consultar]
     * @return [array]                [Array de datos de los reportes Surveys]
     */
    protected function list_surveys($fecha_evento){
        $query_surveys         = $this->query_surveys($fecha_evento);
        $builderview           = $this->builderview($query_surveys);
        $surveyscollection     = $this->surveyscollection($builderview);        
        $list_call_surveys     = $this->FormatDatatable($surveyscollection);

        return $list_call_surveys;
    }


    /**
     * [export Función que permite exportar el reporte de Incoming Calls]
     * @param  Request $request [Retorna datos enviados por POST]
     * @return [array]          [Array con la ubicación de los archivos exportados]
     */
    public function export(Request $request){
        $export_surveys  = call_user_func_array([$this,'export_'.$request->format_export], [$request->days]);
        return $export_surveys;
    }


    /**
     * [query_surveys Función que consulta a la Base de Datos información sobre reportes Sruveys]
     * @param  [string] $days [Fecha a consultar]
     * @return [array]        [Array con datos de la consulta realizada]
     */
    protected function query_surveys($fecha_evento){        
        $days                                   = explode(' - ', $fecha_evento);
        $list_encuesta                          = \DB::select('call sp_data_encuesta("'.$days[0].'","'.$days[1].'")');
        $resuldato_encuesta                     = \DB::select('call sp_data_resultados("'.$days[0].'","'.$days[1].'")');
        $builderesultado                        = $this->builderesultado($resuldato_encuesta);
        $query_surveys['detalle_llamada']       = $list_encuesta;
        $query_surveys['preguntas_respuestas']  = $builderesultado;
        return $query_surveys;
    } 

    protected function builderesultado($resuldato_encuesta){
        $builderesultado = [];
        $secuencia = 1;
        foreach ($resuldato_encuesta as $resuldato) {
            if(!isset($builderesultado[$resuldato->encuesta_id])){
                $builderesultado[$resuldato->encuesta_id]['pregunta_1']     ='-';
                $builderesultado[$resuldato->encuesta_id]['respuesta_1']    ='-';
                $builderesultado[$resuldato->encuesta_id]['pregunta_2']     ='-';
                $builderesultado[$resuldato->encuesta_id]['respuesta_2']    ='-';
                $secuencia = 1;
            }
            $builderesultado[$resuldato->encuesta_id]['pregunta_'.$secuencia]  = $resuldato->pregunta;
            $builderesultado[$resuldato->encuesta_id]['respuesta_'.$secuencia] = $resuldato->respuesta;
            $secuencia++;
        }

        return $builderesultado;
    }

    /**
     * [builderview Función que prepara los datos de la consulta para sermostrados en la vista]
     * @param  [array] $query_surveys [Datos obetenidos de la base de datos de reporte Surveys]
     * @return [array]                [Array con con los datos modificados para la vista]
     */
    protected function builderview($query_surveys){
        $builderview = [];
        $posicion = 0;
        foreach ($query_surveys['detalle_llamada'] as $surveys) {

            if(!isset($query_surveys['preguntas_respuestas'][$surveys->Id])){
                $query_surveys['preguntas_respuestas'][$surveys->Id]['pregunta_1']  ='-';
                $query_surveys['preguntas_respuestas'][$surveys->Id]['respuesta_1'] ='-';
                $query_surveys['preguntas_respuestas'][$surveys->Id]['pregunta_2']  ='-';
                $query_surveys['preguntas_respuestas'][$surveys->Id]['respuesta_2'] ='-';               
            }

            if($surveys->Skill == ''){$surveys->Skill = '-';}

            $builderview[$posicion]['Username']       = $surveys->Username;
            $builderview[$posicion]['Anexo']          = $surveys->Anexo;
            $builderview[$posicion]['Telephone']      = $surveys->Telefono;
            $builderview[$posicion]['Skill']          = $surveys->Skill;
            $builderview[$posicion]['Duration']       = $surveys->Duracion;
            $builderview[$posicion]['Tipo Encuesta']  = $surveys->TipoEncuesta;
            $builderview[$posicion]['Question_01']    = $query_surveys['preguntas_respuestas'][$surveys->Id]['pregunta_1'];
            $builderview[$posicion]['Answer_01']      = $query_surveys['preguntas_respuestas'][$surveys->Id]['respuesta_1'];
            $builderview[$posicion]['Question_02']    = $query_surveys['preguntas_respuestas'][$surveys->Id]['pregunta_2'];
            $builderview[$posicion]['Answer_02']      = $query_surveys['preguntas_respuestas'][$surveys->Id]['respuesta_2'];             
            $builderview[$posicion]['Date']           = $surveys->Fecha;
            $builderview[$posicion]['Hour']           = $surveys->Hora;
            $builderview[$posicion]['Action']         = $surveys->Evento;
            
            
            $posicion ++;
        }

        return $builderview;
    }


    /**
     * [surveyscollection Función que pasa los datos de un Array a una Collection]
     * @param  [array]      $query_calls [Array con los dato de reportes Surveys]
     * @return [collection]              [Collection con datos del repote Sruveys]
     */
    protected function surveyscollection($builderview){
        $surveyscollection                 = new Collector;
        foreach ($builderview as $view) {
            $surveyscollection->push([
                
                'Username'          =>  $view['Username'],
                'Anexo'             =>  $view['Anexo'],
                'Telephone'         =>  $view['Telephone'],
                'Skill'             =>  $view['Skill'],
                'Duration'          =>  $view['Duration'],
                'Tipo Encuesta'     =>  $view['Tipo Encuesta'],
                'Question_01'       =>  $view['Question_01'],
                'Answer_01'         =>  $view['Answer_01'],
                'Question_02'       =>  $view['Question_02'],
                'Answer_02'         =>  $view['Answer_02'],
                'Date'              =>  $view['Date'],
                'Hour'              =>  $view['Hour'],
                'Action'            =>  $view['Action']
            ]);
        }
        return $surveyscollection;
    }


    /**
     * [export_csv Function que retorna la ubicación de los datos a exportar en CSV]
     * @param  [string] $days [Fecha de la consulta]
     * @return [array]        [Array con la ubicación donde se a guardado el archivo exportado en CSV]
     */
    protected function export_csv($days){

        $builderview = $this->builderview($this->query_surveys($days),'export');
        $this->BuilderExport($builderview,'surveys','csv','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/surveys.csv']
        ];

        return $data;
    }


    /**
     * [export_excel Function que retorna la ubicación de los datos a exportar en Excel]
     * @param  [string] $days [Fecha de la consulta]
     * @return [array]        [Array con la ubicación donde se a guardado el archivo exportado en Excel]
     */
    protected function export_excel($days){

        $builderview = $this->builderview($this->query_surveys($days,'surveys'),'export');
        $this->BuilderExport($builderview,'surveys','xlsx','exports');

        $data = [
            'succes'    => true,
            'path'      => ['http://'.$_SERVER['HTTP_HOST'].'/exports/surveys.xlsx']
        ];

        return $data;
    }   


    

}
