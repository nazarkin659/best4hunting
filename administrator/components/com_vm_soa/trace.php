<?php
/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/

/**
 * Gestion de la trace
 *
 * @author      Jean-Paul Olivier <Jpaul.Olivier@laposte.net>
 * @package      JpoVmDol15
 * @version    V0.0
 */
/**
 * Cette classe propose des fonctions de trace.
 *
 * La fonction de trace n'écrit dans le fichier de trace que si la trace est positionnée (fichier de configuration principal)
 * Une bannière d'entête horo datée sera insérée avant la premiére écriture d'une session (envoi d'une page par le navigateur de l'utilisateur)
 */
class trace {
	/**
	 * Flag de tracage
	 */
	var $trace_lvl=0;
	/**
	 * Flag de mémorisation de l'état du fichier trace ouverte ou non
	 */
	var $trace_open=0;
	/**
	 * compteur d'indentation
	 */
	var $indent=0;
	/**
	 * Tableau utilisé pour calculer le temps d'exécution
	 */
	var $tx;

	var $trace_dir;
	/**
	 * Constructeur de la classe trace
	 */
	function trace($trace_lvl)
	{
		/*global $option;
		$this->trace_lvl = $trace_lvl;
		if (file_exists("components"))
		{
			if (!file_exists(JPATH_COMPONENT.DS."/traces"))
			{
				mkdir(JPATH_COMPONENT.DS."/traces");
			}
			$this->trace_dir=JPATH_COMPONENT.DS."/traces";
		}
		else
		{
			if (!file_exists("traces"))
			{
				mkdir("traces");
			}
			$this->trace_dir="traces";
		}*/
	}
	/**
	 * Fonction de tracage
	 *
	 * Cette fonction ne trace que si le flag de tracage est positionn�
	 * Ecriture d'un marquage horodateur si le fichier n'est pas ouvert
	 * Gestion de l'indentation (quand une fonction commence, la trace doit �tre appel�e avec +1, quand elle se termine avec -1
	 */
	function trace_it($data, $lvl=1, $indent=0)
	{
		/*global $option;
		if ($this->trace_lvl >= $lvl)
		{
			$fp=fopen($this->trace_dir."/trace.txt", "a");
			if ($this->trace_open == 0)
			{
				fwrite($fp, "----------------------------------\r\n");
				$DH=date("Ymd H:i:s\r\n");
				fwrite($fp, $DH);
				$this->trace_open = 1;
				$this->indent=0;
			}
			if ($indent == -1)
			{
				$this->indent=$this->indent+$indent;
			}
			for ($i=0; $i<$this->indent; $i++)
			{
				fwrite($fp, ". ");
			}
			if ($indent == +1)
			{
				fwrite($fp, "->");
			}
			if ($indent == -1)
			{
				fwrite($fp, "<-");
			}
			fwrite($fp, $data);
			if ($indent == -1)
			{
				$tf=microtime(true);
				$td=$this->tx[$this->indent+1];
				fwrite($fp, " début : ".$td );
				fwrite($fp, " fin : ".$tf );
				fwrite($fp, ' Temps d\'execution de la fonction : '.round($tf - $td, 4)."\r\n");
			}
			if ($indent == +1)
			{
				$this->indent=$this->indent+$indent;
				$this->tx[$this->indent]=microtime(true);
				fwrite($fp, " début : ".$this->tx[$this->indent]);
			}
			fwrite($fp, "\r\n");
			fclose($fp);
		}*/
	}
	/**
	 * Remise à zéro du fichier de trace
	 *
	 */
	function raz_file()
	{
		/*global $option;
		$fp=fopen($this->trace_dir."/trace.txt", "w");
		fclose($fp);
		$this->trace_lvl=0;*/
	}
	/**
	 * Dump d'une variable dans la sortie html (écran)
	 *
	 */
	function trace_print_r($v, $lvl=1)
	{
	/*	if ($this->trace_lvl >= $lvl)
		{
			echo "<pre>";
			print_r($v);
			echo "</pre>";
		}*/
	}
	/**
	 * Affichage dans la sortie html (écran)
	 *
	 */
	function trace_echo($v, $lvl=1)
	{
		/*if ($this->trace_lvl >= $lvl)
		{
			echo "<br>".$v;
		}*/
	}
	/**
	 * Trace le résultat d'une requ�te sql
	 *
	 */
	function trace_sql_result($result, $lvl=1)
    {
		/*if ($this->trace_lvl >= $lvl)
		{
			$fp=fopen($this->trace_dir."/trace.txt", "a");
			if (get_resource_type($result) != FALSE)
			{
				 $j=0;
				 // Affichage des titres de colonnes
				 while ($j < mysql_NumFields($result))
				{
					fwrite($fp, "($j) ".mysql_FieldName($result,$j)."\r\n");
					$j++;
				}
				  // Affichage du contenu
				 while($tableau=mysql_fetch_row($result))
				{
				   $j=0;
				   while (list($key, $val) = each($tableau))
				  {
					fwrite($fp, "($j) ".$key." ".$val."\r\n");
				  }
				}
			}
			fclose($fp);
		}*/
    }

	/**
	 * affiche un message en pop_up
	 *
	 */
	function pop_up_message($message, $lvl=1)
    {
		/*if ($this->trace_lvl >= $lvl)
		{
			echo '
			<script type="text/javascript">
			<!--
			alert("'.str_replace("\n","\\n",$message).'")
			-->
			</script>';
		}*/
	}
}
?>