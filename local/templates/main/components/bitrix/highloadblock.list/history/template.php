<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult['ERROR']))
{
	echo $arResult['ERROR'];
	return false;
}

if ($arResult['IS_CAPITAN']=='false') LocalRedirect('/personal/pay/history.php');
?>




<div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11 col-md-12">
                   <div class="layout__content-heading-with-btn-back">
                <a href="<?php echo SITE_DIR;?>management-compositional/" class="btn-italic-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 11.62">
                    <path d="M955.22,534.89a1,1,0,0,1,0,1.33l-3,3.27h18.2a.94.94,0,0,1,0,1.88h-18.2l3,3.27a1,1,0,0,1,0,1.32.81.81,0,0,1-1.21,0l-4.49-4.88h0a1,1,0,0,1,0-1.33h0l4.49-4.87A.81.81,0,0,1,955.22,534.89Z" transform="translate(-949.26 -534.62)"></path>
                  </svg> Назад
                </a>
                <h1 class="text-center">
                  История операций
                </h1>
              </div>
                </div>
              </div>
              <div class="history-operation">
                <div class="flex-table">
                  <div class="flex-table--header bg-default">
                    <div class="flex-table--categories">
                      <span>Тип валюты</span>
                      <span>Дата</span>
                      <span>Тип операции</span>
                      <span>Наименование операции</span>
                           <span>Автор операции</span>
               <span>Баланс</span>
                      <span>Количество</span>
                    </div>
                  </div>
                  <div class="flex-table--body">
                  <?foreach ($arResult['rows'] as $row): 	       
                    $i = 0;
                    $p='';	         
                    ?>  
                    <div class="flex-table--row">
                    
                    <? $i = 0;
				$j=0;
		        foreach(array_keys($arResult['tableColumns']) as $col): ?>
				<?
				//echo '<pre>';print_r($row);echo '</pre>';
				$i++;$p1='Приход';
				if ($i!= 1 & $i!= 2  & $i!= 10 &  $i!= 13){
		                  $finalValue = $row[$col];				                 
		                  if ($i== 5){ if (strcasecmp(mb_strtolower($finalValue, 'UTF-8') ,mb_strtolower($p1, 'UTF-8'))=='0'){$p='+';$j=0;}else{$p='-';$j=1;}} 
		                  if ($i!= 8 & $i!= 9 & $i!= 10){?><span  class="history-operation__param-wrap <?  if ($j==1){ echo 'span__red';}  ?>" >
		         <div class="history-operation__param">
		                 <? if ($i== 3){echo 'Тип Валюты';}
		                  if ($i== 4){echo 'Дата';}
		                  if ($i== 5){echo 'Тип операции';}
		                  if ($i== 6){echo 'Наименование операции';}
		                  if ($i== 7){echo 'Автор операции';}
		                  if ($i== 8){echo 'Баланс';}
		                  if ($i== 9){echo 'Количество';}
		                ?>
		                  </div><?if ($i== 12){?><div class="history-operation__arrival"><? } ?>
		                  		 <?  if ($i== 12){ echo $p;}  ?>
		                  	<? if ($i== 8){?>" class="core-team__user-link"><? } ?>							
							<? if ($i== 7){?><div class="core-team__user"><div class="core-team__user-avatar" style="background-image: url(	<? }
		                  } ?>						
							
		                  
						<?=$finalValue?><?if ($i== 12){?></div><? } ?>
						<? if ($i== 7){?>)"> 

                          </div>
                        <a href="<? } ?>
                        <? if ($i== 8){?>" class="core-team__user-link"><? }?>
                        <? if ($i== 9){?></a></div><? }?>
						<? if ($i!= 7 & $i!= 8 ){?></span><? }?><? } ?>
					<? endforeach; ?>
                    </div>
       <?endforeach; ?>
                  </div>
               
                </div>
                <div class="history-operation__show-more"><?php 
	    if ($arParams['ROWS_PER_PAGE'] > 0):
	       $APPLICATION->IncludeComponent(
		  'bitrix:main.pagenavigation',
		  'ajax_for_history',
		      array(
			'NAV_OBJECT' => $arResult['nav_object'],
			'SEF_MODE' => 'N',
		  ),
		  false
	   );
        endif;
        ?>
                  
                </div>
              </div>
            </div>

<script type="text/javascript">
    BX.ready(function(){
        $(document).on('click', '.load-more-items', function(){
            var targetContainer = $('.flex-table--body'),
                url =  $('.load-more-items').attr('data-url');
            if (url !== undefined) {
                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'html',
                    success: function(data){
                        $('.load-more-items').attr('data-count', $('.load-more-items').attr('data-count')- $('.load-more-items').attr('data-page'));
                        if($('.load-more-items').attr('data-count')<$('.load-more-items').attr('data-page')) $('.load-more-items').remove();
                        var elements = $(data).find('.flex-table--row');
                        targetContainer.append(elements);

                    }
                });
            }

        });
    });
</script>















