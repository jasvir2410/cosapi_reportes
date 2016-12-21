
              <div class="table-responsive">
                  <table align="center" class="table table-responsive table-bordered table-condensed table-hover">
                    <thead>
                      <tr>
                        <th>Annexed</th>
                        <th>Agent</th>
                        <th>Status</th>
                        <th>Queue</th>
                        <th>Phone Number</th>
                        <th>Total Duration</th>
                        <th>Total Calls</th>
                        <th>Exit</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($list_agent_details as $agent)
                        <tr>
                          <td>
                            {{$agent->anexo}}
                            <span class="pull-right-container">
                              <span class="img img-circle pull-right bg-{{$agent->status_anexo_color}}">
                                <i class="{{$agent->status_anexo_icon}}" style="padding: 3.5px; " aria-hidden="true"></i>
                              </span>
                            </span>
                          </td>
                          <td>{{$agent->nombre_completo}}</td>
                          <td align="center">
                            <span class="label label-{{$agent->type}}">
                              <i class="{{$agent->icon}}" style="padding: 1px;" aria-hidden="true"></i>
                              {{$agent->nombre_ultimoevento}}
                            </span>
                          </td>
                          <td align="center">{{$agent->name_queue}}</td>
                          <td align="center">{{$agent->number_origin}}</td>
                          <td align="center">{{$agent->time_call}}</td>
                          <td align="center">{{$agent->totalcalls}}</td>
                          <td align="center">
                            <button onclick="desloguear_agente('{{$agent->anexo}}','{{$agent->username}}');">
                              <i class="fa fa-key" aria-hidden="true"></i>
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
            </div>