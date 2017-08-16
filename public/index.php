<?php

?>
<!DOCTYPE html>
<html>
<head>
	<title>Station</title>
	<link rel="stylesheet" type="text/css" href="/style.css">
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="/main.js"></script>
</head>
<body>

<section class="error-server-connection-lost text-center hide">Соединение с сервером потеряно, <a href="javascript:window.location.reload();">обновите страницу</a></section>
<div style="height:20px;"></div>

<section class="stadium-info">
<dir class="flex-container">
<?php for( $s='A'; $s < 'D'; $s++ ){?>
<div class="flex-row">
<p class="text-center">Sector: <?=$s;?></p>
<table class="stadium">
<?php for( $i=1; $i < 11; $i++ ){?>
	<tr class="row">
	<td class="row-title">Ряд: <?php echo $i;?></td>
	<?php for( $j=1; $j < 11; $j++ ){?>
		<?if(rand(0,10) == $j):?>
			<td class="u-place place-reserved" data-uid="<?="$s-$i-$j";?>" data-price="<?=(12-$i)*120;?>"><?php echo $j;?></td>
		<?elseif(rand(0,10) == $j):?>
			<td class="u-place place-in-proccess" data-uid="<?="$s-$i-$j";?>" data-price="<?=(12-$i)*120;?>"><?php echo $j;?></td>
		<?else:?>
			<td class="u-place place-free action--selectPlace" data-uid="<?="$s-$i-$j";?>" data-price="<?=(12-$i)*120;?>"><?php echo $j;?></td>
		<?endif;?>
	<?php }?>
	</tr>
<?php }?>
</table>
</div>
<?}?>
</dir>

<hr>
<div>
	<div><div class="place-free"></div> - Свободные места</div>
	<div><div class="place-reserved"></div> - Зарезервированые места</div>
	<div><div class="place-in-proccess"></div> - В процессе регистрации места</div>
</div>
</section>

<section class="reservation-info hide">
	Выбранные места: <div class="data-result"></div>
	Общая сумма: <div class="total-summ">0</div>
	<button class="action--makeOrder">Забронировать</button>
</section>

<section class="new-order hide">
	<button class="action--cancelOrder">Вернутся</button>
	<form>
		Имя: 
		<input type="text" name="name">
		<br>
		Телефон: 
		<input type="text" name="phone">
		<br>
		На сумму: <span class="total-summ">0</span>
		<br>
		<input type="submit" name="order" value="Забронировать">
	</form>
</section>

</body>
</html>