@extends('layouts.blank')
@section('content')

<style type="text/css">
.wrap {
   width:700px;
   margin:0 auto;
}

.appointmentdetails{ background-color:#eaeaea;
                    }

.appointmentdetails thead{ background-color:#b0c4de;
                    }
tr.schnormal {color:#000000;}
tr.schhistory {color:#580000;}
tr.schnoshow {color:#FF0000;}
tr.schcancel {color:#808000;}
</style>

<div id='appontmentdetails' class='wrap appointmentdetails'>
<table class="properties wrap">
<thead>
    <tr>
    <th>Date</th>
    <th>Time</th>
    <th>Resource</th>
    <th>Duration</th>
    <th>Visit Type</th>
    <th>Where</th>
    <th>Notes</th>
    </tr>
</thead>

    @foreach ($appointments as $appointment)
      <tr class="{{$appointment['display']}}">
          <td>{{date('m/d/y',strtotime($appointment['startf']))}}</td>
          <td>{{date('h:ia',strtotime($appointment['startf']))}}</td>
          <td>{{preg_replace('~\b(\w)|.~', '$1', $appointment['resource'])}}</td>
          <td>{{ (strtotime($appointment['endf']) - strtotime($appointment['startf']))/60}}</td>
          <td>{{ preg_replace('~\b(\w)|.~', '$1', $appointment['appointment_type'])}}</td>
          <td>{{preg_replace('~\b(\w)|.~', '$1', $appointment['facility'])}}</td>
          <td>{{$appointment['notes']}}</td>
      </tr>
    @endforeach
</table>
@stop