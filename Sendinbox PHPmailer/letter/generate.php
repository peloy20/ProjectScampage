<?php 
/**
* 
*/
class Revan 
{
	
	
	function random_text($jenis , $length = 10 , $lowup = 'up'){
		 switch ($jenis) {
            case 'textrandom':
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
            case 'numrandom':
                $characters = '0123456789';
            break;
            case 'textnumrandom':
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
            
            default:
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        switch ( strtolower($lowup) ) { 
            case 'low':
                $randomString = strtolower( $randomString );
            break;
            case 'up':
                $randomString = strtoupper( $randomString );
            break;
            
            default:
                $randomString = strtolower( $randomString );
            break;
        }
        return $randomString;
	}
}

 ?>