

    <!-- /.box-header -->
      <div class="table-responsive">
          <table align="center" class="table table-responsive table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>Contexto</th>
                <th>Numero CallerID</th>
                <th>Name </th>
                <th>Seconds</th>
                <th>Queue</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($showEncoladas as $key => $showEncolada)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $showEncolada['Contexto'] }}</td>
                  <td>{{ $showEncolada['CallerIDNum'] }}</td>
                  <td>{{ $showEncolada['CallerIDName'] }}</td>
                  <td>{{ conversorSegundosHoras($showEncolada['Seconds'], true) }}</td>
                  <td>{{ $showEncolada['Queue'] }}</td>
                </tr>
                @endforeach
            </tbody>
          </table>
    </div>
    <!-- /.box-body -->
