<?php 
class PostCondition
{
	public function PostConditions($folder){
		exec("rm -rf ".$folder."/");
	}
}