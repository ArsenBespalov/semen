<?php
$path='/var/www/html/';
include_once $path.'db.php';
/*if (isset($_POST['tc_edit_id_service']) && isset($_POST['tc_edit_id_tenant'])) {

	$id_tenant=$_POST['tc_edit_id_tenant'];
	$id_service=$_POST['tc_edit_id_service'];
	
	$ten=mysql_query('SELECT * FROM  `the_tenant` where id_tenant="'.$id_tenant.'"');
	if ($num=mysql_fetch_assoc($ten)) {
		$square=$num['square'];
		$quantity=$num['quantity_of_lodger'];
	} else $square = $quantity = 0;
	
	$ser=mysql_query('SELECT * FROM  `service` where id_service='.$id_service);
	if ($row=mysql_fetch_assoc($ser)) {
		$kvm1=$row['price_for_1_sqr_metre_k1'];
		$kvm2=$row['price_for_1_sqr_metre_k2'];
		$kvc1=$row['price_for_1_people_k1'];
		$kvc2=$row['price_for_1_people_k2'];
	} else $kvm1 = $kvm2 = $kvc1 = $kvc2 = 0;
	
	$sum=($kvm1*$kvm2*$square)+($kvc1*$kvc2*$quantity);
  echo json_encode(array("amount"=>$sum));
	
} else*/
if (isset($_POST['tc_new_id_house']) && isset($_POST['tc_new_number_flat']) && isset($_POST['tc_new_id_service'])) {

	$id_house=$_POST['tc_new_id_house'];
	$nf=$_POST['tc_new_number_flat'];
  $id_service=$_POST['tc_new_id_service'];
	$ten=$my->query('SELECT * FROM  `the_tenant` where id_house="'.$id_house.'" and number_flat="'.$nf.'"');
	if($num=$ten->fetch_assoc()) {
  $id=$num['id_tenant'];
  $fio=$num['surname'].' '.$num['name_tenant'].' '.$num['patronomic'];
  $kolvo=$num['quantity_of_lodger'];
  $s=$num['square'];
  $id_tenant=$num['id_tenant'];
  }
	else $fio = "[Данные отсутствуют. Уточните номер дома и квартиры]";

  
  $ser=$my->query('SELECT * FROM  `service` where id_service="'.$id_service.'"');
	if($num=$ser->fetch_assoc()) {
  $sum=$s*$num['price_for_1_sqr_metre_k1']*$num['price_for_1_sqr_metre_k2']+$kolvo*$num['price_for_1_people_k1']*$num['price_for_1_people_k2'];
  }
  echo json_encode(array("fio"=>$fio,"kolvo"=>$kolvo,"s"=>$s,"amount"=>$sum,"id_tenant"=>$id_tenant));
//Карта квартиросъемщика Редактирование Услуга 
} elseif (isset($_POST['tc_edit_id_tenant']) && isset($_POST['tc_edit_id_service'])) {
  $ten=$my->query('SELECT * FROM  `the_tenant` where id_tenant='.$_POST['tc_edit_id_tenant']);
	$num=$ten->fetch_assoc(); 
  $kolvo=$num['quantity_of_lodger'];
  $s=$num['square'];
  $ser=$my->query('SELECT * FROM  `service` where id_service='.$_POST['tc_edit_id_service']);
	$num=$ser->fetch_assoc();
  $sum=$s*$num['price_for_1_sqr_metre_k1']*$num['price_for_1_sqr_metre_k2']+$kolvo*$num['price_for_1_people_k1']*$num['price_for_1_people_k2'];
  echo json_encode(array("amount"=>$sum));	
  } elseif (isset($_POST['tc_edit_id_card']) && isset($_POST['tc_edit_id_service'])) {
  $ten=$my->query('SELECT * FROM  `tenant_card` where id_card='.$_POST['tc_edit_id_card']);
	$num=$ten->fetch_assoc(); 
  $q=$my->query('SELECT * FROM  `the_tenant` where id_tenant='.$num['id_tenant']);
  $ten=$q->fetch_assoc();
  $kolvo=$ten['quantity_of_lodger'];
  $s=$ten['square'];
  $ser=$my->query('SELECT * FROM  `service` where id_service='.$_POST['tc_edit_id_service']);
	$w=$ser->fetch_assoc();
  $sum=$s*$w['price_for_1_sqr_metre_k1']*$w['price_for_1_sqr_metre_k2']+$kolvo*$w['price_for_1_people_k1']*$w['price_for_1_people_k2'];
  echo json_encode(array("amount"=>$sum));
  }
  if (isset($_POST['ai_month']) && isset($_POST['ai_year']) && isset($_POST['month_text'])) {
  $result='';
  $q=$my->query('SELECT * FROM  `tenant_card` tc where counter=0');
	while (@$row=$q->fetch_assoc()) {
     $result='insert into accrued_items values ("" , "' .date("Y.m.d",mktime(0, 0, 0, $_POST['ai_month']+1, 0, $_POST['ai_year'])).
     '" , '.$row['id_tenant'].' , '.$row['id_service'].' , '.$row['amount'].' , "начислено за '.$_POST['month_text'].' '.$_POST['ai_year'].'"); ';
  $res=$my->query($result);
  }
  echo json_encode(array("result"=>'Начисление по нормативу выполнено!'));
  }
  if (isset($_POST['cc_number_flat']) && isset($_POST['cc_adress'])) {
    $id_house=$_POST['cc_adress'];
	  $nf=$_POST['cc_number_flat'];
   	$ten=$my->query('SELECT * FROM  `the_tenant` where id_house="'.$id_house.'" and number_flat="'.$nf.'"');
    $row=$ten->fetch_assoc();
    $z=$my->query('SELECT c.id_counter,s.name_service, (select end_count from  calculation_counter where counter=c.id order by date LIMIT 1) 
    as end_count, round((s.price_for_1_sqr_metre_k1+s.price_for_1_people_k1),2) as price, s.id_sertype FROM counter c 
    join tenant_card tc on tc.id_card=c.id_card join service s on s.id_service=tc.id_service where tc.id_tenant='.$row['id_tenant'].' order by s.id_sertype');
//     echo $id_house;
//     echo $nf;
//     echo 'SELECT c.id_counter,s.name_service, (select end_count from  calculation_counter where counter=c.id order by date LIMIT 1) 
//     as end_cound, round((s.price_for_1_sqr_metre_k1+s.price_for_1_people_k1),2) as price, s.id_sertype FROM counter c 
//     join tenant_card tc on tc.id_card=c.id_card join service s on s.id_service=tc.id_service where tc.id_tenant='.$row['id_tenant'];
//     exit;  
    $cc='';
    $cc.= "<form name='new' action='index.php' method='get'>";
   
    $cc.=  "<label for='tc_fio'>Дата:</label>";
    $cc.= "<input type='text' id='cc_date' data-form='date' readOnly>  <br>";
       
    $cc.=  "<label for='tc_fio'>Лицевой счет:</label>";
    $cc.= "<input type='text' id='tc_id_tenant' disabled='disabled' value=".$row['id_tenant'].">  <br>";
      
    $cc.=  "<label for='tc_fio'>ФИО:</label>";
    $cc.= "<input type='text' id='tc_fio' disabled='disabled' value=\"".$row['surname']." ".$row['name_tenant']. " ".$row['patronomic']."\"> <br>";
    
    $cc.=  "<label for='tc_s'>Площадь:</label>";
    $cc.= "<input type='text' id='tc_S'  disabled='disabled' value=".$row['square']."> <br>";
    
    $cc.=  "<label for='tc_kolvo'>Количество человек:</label>";
    $cc.= "<input type='text' id='tc_kolvo'  disabled='disabled' value=".$row['quantity_of_lodger']."> <br>";
    $cc.= "<input type='text' id='temp'> <br>";
    $cc.= "</form>";
    
    $cc.= "<table id='cc_table' border=1 cellspacing=0 cellpadding=2 width=680 px align='center'>";
    $cc.= "<tr>";
    $cc.= " <td> №</td>";
    $cc.= " <td> № счетчика </td>";
    $cc.= " <td> Услуга </td>";
    $cc.= " <td> Начальные показания </td>";
    $cc.= " <td> Конечные показания </td>";
    $cc.= " <td> Объем </td>";
    $cc.= " <td> Цена </td>";
    $cc.= "<td>Сумма</td>";
    $cc.= " </tr>";
    $k=1;
    
    while (@$zz=$z->fetch_assoc()) {
    $vodd=$my->query('SELECT s.id_sertype FROM  `service` s join tenant_card tc on s.id_service=tc.id_service 
     where tc.id_card='.$zz['id_counter']);
    $vod=$vodd->fetch_assoc();
    if ($vod['id_sertype']==2) {
    $cc.= " <tr id='vod'> "; }
    else {$cc.= " <tr> ";}
   	$cc.= "<td>".$k."</td>";
  	$cc.= "<td>".$zz['id_counter']."</td>";
    $cc.= "<td >".$zz['name_service']."</td>";
    if ($vod['id_sertype']==1) {
        if ($zz['end_count']<>'') {$cc.= "<td>".$zz['end_count']."</td>";} else {$cc.= "<td>0</td>";}
    } else {
    $cc.= "<td></td>";
    }
    $cc.= "<td></td>";
    $cc.= "<td></td>";
    $cc.= "<td>".$zz['price']."</td>";
    $cc.= "<td></td>";
    $cc.= " <tr>";
    $k++;
    }
    $cc.= " <tr>";
   	$cc.= "<td></td>";
  	$cc.= "<td></td>";
    $cc.= "<td></td>";
    $cc.= "<td></td>";
    $cc.= "<td></td>";
    $cc.= "<td></td>";
    $cc.= "<td>Итого</td>";
    $cc.= "<td></td>";
    $cc.= " <tr>";
    $cc.= " </table><br>";
    $cc.= "<button type=\"button\" id='cc_save' class='button'>Сохранить</button> " ;
    $cc.= "<button type=\"button\" id='cc_print' class='button'>Печать</button> <br><br>" ;
    $cc.= "<div id='cc_data'></div>";
    $cc.= "<form>";
    
  echo json_encode(array("result"=>$cc));   
  }
  if (isset($_POST['hs_counter'])) {
     $result='';
     $result.=  "<label for='hs_counter_direct'>Количество счетчиков прямой подачи:</label>";
    $result.= "<input type='text' id='hs_counter_direct'>  <br>";
       
    $result.=  "<label for='hs_counter_return'>Количество счетчиков обратной подачи:</label>";
    $result.= "<input type='text' id='hs_counter_return'>  <br>";
    echo json_encode(array("result"=>$result));
  }
  if (isset($_POST['cc_text'])) {
      $result=preg_split('/;/',$_POST['cc_text'],-1, PREG_SPLIT_NO_EMPTY);
      foreach ($result as &$value) {
        $ten=$my->query($value);  
      }  
  }
?>                     