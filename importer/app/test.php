<?php
\ui\load_lib('cache');
\ui\benchmark('Start TEST');
if(\ui\cache\start2(false,10)){
  echo 'hello!';
  for($i=0;$i<exp(3);$i++){
    $r1=rand();
    $r2=rand();
    if($r1>$r2)$r3=$r2%$r2;
    else $r3=rand()/exp(rand());
    echo $r2,$r1.$r3;
    if(mt_rand()%10==0)echo PHP_EOL.'<br>';
  }
  \ui\cache\stop();
}else{
  echo 'SHOWED CACHED DATA!';
}
\ui\benchmark('Stop TEST');