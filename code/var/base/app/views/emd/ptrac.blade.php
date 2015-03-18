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

.shccol{
 width:100px;
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
<table class="properties inwrap">
<thead>
    <tr>
    <th class="shccol">Date</th>
    <th class="shccol">Time</th>
    <th class="shccol">Resource</th>
    <th class="shccol">Duration</th>
    <th class="shccol">Visit Type</th>
    <th class="shccol">Where</th>
    <th class="shccol">Notes</th>
    </tr>
</thead>
</table>
<div id='appontmentdetails' class='inwrap appointmentdetails' style="overflow: scroll; width: 650px; height: 150px;">
<table class="properties inwrap">
<tbody>
    @foreach ($appointments as $appointment)
      <tr class="{{$appointment['display']}}">
          <td class="shccol">{{date('m/d/y',strtotime($appointment['startf']))}}</td>
          <td class="shccol">{{date('h:ia',strtotime($appointment['startf']))}}</td>
          <td class="shccol">{{preg_replace('~\b(\w)|.~', '$1', $appointment['resource'])}}</td>
          <td class="shccol">{{ (strtotime($appointment['endf']) - strtotime($appointment['startf']))/60}}</td>
          <td class="shccol">{{ preg_replace('~\b(\w)|.~', '$1', $appointment['appointment_type'])}}</td>
          <td class="shccol">{{preg_replace('~\b(\w)|.~', '$1', $appointment['facility'])}}</td>
          <td class="shccol" data="{{preg_replace('~\b(\w)|.~', '$1', $appointment['notes'])}}">
          <?php
            if(trim($appointment['notes']) <> "" ){
                    //if longer than 10 chars
                   if(strlen(trim($appointment['notes'])) > 100){
                       echo "<a href=\"#\" class=\"opennotes\">" . substr(trim($appointment['notes']),0,100  . "...</a>";
                   }else{
                        echo "<a href=\"#\" class=\"opennotes\">" . trim($appointment['notes']) . "</a>";
                   }
            }else{
                echo "&nbsp;";
            }
          ?>
          </td>
      </tr>
    @endforeach
    </tbody>
</table>
@stop