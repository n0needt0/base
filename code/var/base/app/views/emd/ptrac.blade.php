@extends('layouts.blank')
@section('content')

<style type="text/css">
.wrap {
   width:700px;
   margin:0 auto;
}

.invoicedetails{ background-color:#eaeaea;
                    }

.invoiceheader{ background-color:#b0c4de;
                    }
</style>

<div id='appontmentdetails' class='wrap appointmentdetails'>
<table class="properties">
    <tbody>
    <tr>
    <th>When</th>
    <th>Service</th>
    <th>Provider</th>
    <th>Where</th>
    <th>Notes</th>
    </tr>

    @foreach ($appointments as $appointment)
      <tr>
          <td>{{date('D M-d h:iA',strtotime($appointment['startf']))}}</td>
          <td>{{$appointment['appointment_type']}}</td>
          <td>{{$appointment['resource']}}</td>
          <td>{{$appointment['facility']}}</td>
          <td>{{$appointment['notes']}}</td>
      </tr>
    @endforeach
    </tbody>
</table>
@stop