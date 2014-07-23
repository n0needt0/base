<?php namespace Lasdorf\Es;

use Lasdorf\Es\EsBase;
use Illuminate\Support\Facades\Config as Config;  //to get configs
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Log; //to log screw ups
use \Elasticsearch\Client;

Class EsApi extends EsBase{

    public function __construct(){
        \Log::info($this->sig . " api call start:" . time());
        $this->es = new \Elasticsearch\Client(array('hosts'=> Config::get('elastic.eshosts')));
        parent::set_debug_level( Config::get('elastic.dayes.debug') );
        $this->bulkes=array('body'=>array()); //structure to keep bunch of docs together for bulk insert
        $this->bulklimit = 4000; //number of documents in bulk struct double it to what you want
    }

    public function me()
    {
        echo "working fbg api";
        die;
    }

    public function flush_index($index_name)
    {
        try {

            if( $this->es->indices()->exists(array('index'=>$index_name)) )
            {
                $this->es->indices()->delete(array('index'=>$index_name));
            }
        } catch (Exception $e) {
            $this->debug($e->getMessage());
        }
    }

    public function insert_doc($doc)
    {
        try
        {
            $this->es->index($doc);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * Selects meta data associated with invoices
     */
    public function emds_struct_to_es($index)
    {

        $result = array();

        $res = DB::connection('emds')->table( "sysobjects")->select("name")->where("xtype", "=", "U")->where('name','NOT LIKE','CRPT_CrystalReport%')->where('name','NOT LIKE', 'WordDefinition_%')->where('name','NOT LIKE','RNDS_%')->where('name','NOT LIKE','LKUP_%')->where('name','NOT LIKE','AUDT_%')->where('name', 'NOT LIKE','TMPL_%')->where('name','NOT LIKE','DOQIT%')->get();

        foreach($res as $r)
        {
            $columns = DB::connection('emds')->select(" EXEC sp_columns " . $r->name);

            $columnnames = array();
            $select = '';

            foreach($columns as $column)
            {
                $this->debug( "." );

                if(stripos($column->COLUMN_NAME, 'guid') !== false || $column->TYPE_NAME == 'xml')
                {
                }
                else
                {
                    $select = $select . '"' . $column->COLUMN_NAME . '",';
                    $columnnames[$column->COLUMN_NAME] = $column->TYPE_NAME;
                }
            }

            $select = trim($select, "," );

            //now grab whole table data for each given column
            $data = DB::connection('emds')->table( $r->name)->select(DB::raw($select))->get();

            foreach($data as $row)
            {
                $body = "";
                $i=0;
                foreach($columnnames as $column=>$val)
                {
                    if(!empty($row->$column) && strlen($row->$column)>10 && !stripos($column, 'guid'))
                    {
                        $body = $body . $column . ':' . $this->cleanstr($row->$column) . ' ';
                        $i++;
                    }
                }

                $esdata = array('index'=>$index,
                                'type'=>'emd:' . $r->name . ':' . $column ,
                                'body'=> array('Source'=>'emd:' . $r->name . ':' . $column, 'Body'=>$body )
                );
                if($i>1)
                {
                    $this->bulk_es_insert($esdata);
                }
            }
        }

        //flush whats left

        $this->bulk_es_insert(array(), true);

        return;
    }

    /**
     * adds docs into buk data structure for bulk insert
     * @param unknown $doc
     */
    function bulk_es_insert($doc, $force=false)
    {
        if($force)
        {
            if(!count(count($this->bulkes)))
            {
                //nothing to do;
                return;
            }
            //flush to es
            $this->add_to_es($this->bulkes, true);
            $this->bulkes = array('body'=>array());
            return;
        }

        //else continue with add on
        $i = count($this->bulkes['body']);

        if($i < $this->bulklimit)
        {
            $this->debug( "doc count $i ");
            //add to doc struct
            $this->bulkes['body'][] = array('index' => array('_index'=>$doc['index'],'_type'=>$doc['type']));
            $this->bulkes['body'][] =$doc['body'];
        }
          else
        {
            $this->debug( "flush to es" );
            //flush to es
            $this->add_to_es($this->bulkes, true);
            $this->bulkes = array('body'=>array());
        }
    }


    /**
     *  removes unsafe for json chars from strings
     *  @param unknown $str
     */

    function cleanstr($str,$replacewith='')
    {
        return preg_replace('/[\x00-\x1F\x80-\xFF\'\"]/', $replacewith,$str);
    }


    function add_trac_to_es($trac, $index)
    {

        $res = DB::connection($trac)->table( "ticket")->get();
        foreach($res as $row)
        {
            $esdata = array('index'=>$index, 'type'=>$trac, 'body'=>array('Ticket'=>array(), 'Meta'=>array()));

            foreach($row as $key=>$val)
            {
                if($key == 'id')
                {
                    $esdata['body']['id'] = $val;
                    $esdata['body']['Uri'] = "https://" . $trac . ".helppain.net/ticket/" . $row->id;
                    $esdata['body']['Source'] = $trac;
                }
                $esdata['body']['Ticket'][$key] = $this->cleanstr($val);
            }

                        //now get details
            $details = DB::connection($trac)->table( "ticket_custom")->where("ticket","=",$row->id)->get();

            foreach($details as $drow)
            {
                $esdata['body']['Meta'][$drow->name] = $this->cleanstr($drow->value);
            }

            $comments = DB::connection($trac)->table( "ticket_change")->where("ticket","=",$row->id)->where('field','=','comment')->get();

             $esdata['body']['Body'] = '';

            foreach($comments as $crow)
            {
                $esdata['body']['Body'] .= $crow->author . ':' . $crow->newvalue;
            }

            $this->add_to_es($esdata);
        }
    }

    function add_to_es($data, $bulk=false)
    {
        $this->debug($data);

        try {
            if($bulk)
            {
                $this->es->bulk($data);
            }
              else
            {
                $this->es->index($data);
            }
                return true;
        } catch (Exception $e) {

            echo $e->getMessage();
            return false;
        }
    }
}