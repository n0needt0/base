@extends('layouts.blank')
@section('content')

<style type="text/css">
.wrap {
   width:650px;
   margin:0 auto;
}

.inwrap {
   width:650px;
   margin:0 auto;
}

.appointmentdetails{ background-color:#eaeaea;
                    }

.appointmentdetails thead{ background-color:#b0c4de;
                    }
.schnormal {color:#000000;}
.schhistory {color:#580000;}
.schnoshow {color:#FF0000;}
.schcancel {color:#808000;}
</style>

<h3>EMDs Schedule</h3>

<div id='appontmentdetails' class='wrap appointmentdetails'>

<table class="properties" width="100%">
<tr>
<td class="schnormal">Active</th>
<td class="schhistory">History</th>
<td class="schnoshow">Noshow</th>
<td class="schcancel">Cancelled</th>
</tr>
</table>
<div id='appontmentdetails' class='inwrap appointmentdetails' style="overflow: scroll; width: 650px; height: 150px;">

<table class="properties inwrap">
<thead>
    <tr>
    <th>Date</th>
    <th>Time</th>
    <th>Resource</th>
    <th>Duration</th>
    <th>Visit Type</th>
    <th>Where</th>
    </tr>
</thead>
<tbody>
    @foreach ($appointments as $appointment)
      <tr class="{{$appointment['display']}}">
          <td>{{date('m/d/y',strtotime($appointment['startf']))}}</td>
          <td>{{date('h:ia',strtotime($appointment['startf']))}}</td>
          <td>{{preg_replace('~\b(\w)|.~', '$1', $appointment['resource'])}}</td>
          <td>{{ (strtotime($appointment['endf']) - strtotime($appointment['startf']))/60}}</td>
          <td>{{ preg_replace('~\b(\w)|.~', '$1', $appointment['appointment_type'])}}</td>
          <td>{{preg_replace('~\b(\w)|.~', '$1', $appointment['facility'])}}</td>
      </tr>
    @endforeach
    </tbody>
</table>
@stop