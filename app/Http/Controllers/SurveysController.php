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
    protected function query_surveys($days){
        $days           = explode(' - ', $days);
        $query_surveys =  Survey::select_fechamod()
                                    ->with('anexo')
                                    ->with('user')
                                    ->filtro_days($days)
                                    ->get()
                                    ->toArray();
        return  $query_surveys;
    }


    /**
     * [builderview Función que prepara los datos de la conuslta para sermostrados en la vista]
     * @param  [array] $query_surveys [Datos obetenidos de la base de datos de reporte Surveys]
     * @return [array]                [Array con con los datos modificados para la vista]
     */
    protected function builderview($query_surveys){
        $builderview = [];
        $posicion = 0;
        foreach ($query_surveys as $surveys) {

            $builderview[$posicion]['username']    = $surveys['user']['username'];
            $builderview[$posicion]['anexo']       = $surveys['anexo']['name'];
            $builderview[$posicion]['telephone']   = $surveys['origen'];
            $builderview[$posicion]['skill']       = $surveys['opcion_ivr'];
            $builderview[$posicion]['duration']    = conversorSegundosHoras($surveys['duracion'],false);
            $builderview[$posicion]['answer']      = $surveys['respuesta_01'];
            $builderview[$posicion]['date']        = $surveys['fechamod'];
            $builderview[$posicion]['hour']        = $surveys['timemod'];
            $builderview[$posicion]['action']      = 'Colgo Cliente';
            
            
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
                'username'    => $view['username'],
                'anexo'       => $view['anexo'],
                'telephone'   => $view['telephone'],
                'skill'       => $view['skill'],
                'duration'    => $view['duration'],
                'answer'      => $view['answer'],
                'date'        => $view['date'],
                'hour'        => $view['hour'],
                'action'      => $view['action']
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
