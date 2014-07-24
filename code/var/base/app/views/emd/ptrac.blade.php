@extends('layouts.blank')
@section('content')

<style type="text/css">
.wrap {
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


<div id='appontmentdetails' class='wrap appointmentdetails' style="overflow: scroll; width: 700px; height: 150px;">
<table class="properties wrap">
<tr>
<td class="schnormal">Active</th>
<td class="schhistory">History</th>
<td class="schnoshow">Noshow</th>
<td class="schcancel">Cancelled</th>
</tr>
</table>
<table class="properties wrap">
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
</table>
@stop