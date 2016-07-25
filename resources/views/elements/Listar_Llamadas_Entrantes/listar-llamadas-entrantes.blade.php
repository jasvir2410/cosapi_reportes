<input type="hidden" name="_token" value="{!! csrf_token() !!}">
<p></p>
<div class="row" id="qvacio">
    <div class="col-md-12">
        <div class="panel panel-default">
            <ul class="nav nav-tabs nav-justified nav-pills ">
                <li role="presentation" class="active" id="btnAtendidas" >
                    <a href="#">
                        <span class="glyphicon glyphicon-earphone" aria-hidden="true"></span>
                        <b style="margin-left:5px;">Atendidas</b>
                    </a>
                </li>
                <li role="presentation" id="btnTransferidas">
                    <a href="#">
                        <span class="glyphicon glyphicon-random" aria-hidden="true"></span>
                        <b style="margin-left:5px;">Transferidas</b>
                    </a>
                </li>
                <li role="presentation" id="btnAbandonadas">
                    <a href="#">
                        <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
                        <b style="margin-left:5px;">Abandonadas</b>
                    </a>
                </li>
            </ul>


            <div class="panel-body"  id="tblAtendidas">
                <div class="panel-body" id="cuerpo0">
                    <table id="reporte-estados0" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hour</th>
                                <th>Telephone</th>
                                <th>Agent</th>
                                <th>Skill</th>
                                <th>Duration</th>
                                <th>Action</th>
                                <th>Wait Time</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="panel-body"  id="tblTransfer">
                <div class="panel-body" id="cuerpo1">
                    <table id="reporte-estados1" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hour</th>
                                <th>Telephone</th>
                                <th>Agent</th>
                                <th>Skill</th>
                                <th>Duration</th>
                                <th>Action</th>
                                <th>Wait Time</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="panel-body"  id="tblAbandonadas">
                <div class="panel-body" id="cuerpo2">
                    <table id="reporte-estados2" class="table table-bordered display nowrap table-responsive" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hour</th>
                                <th>Telephone</th>
                                <th>Agent</th>
                                <th>Skill</th>
                                <th>Duration</th>
                                <th>Action</th>
                                <th>Wait Time</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>


        </div>
    </div>
</div>