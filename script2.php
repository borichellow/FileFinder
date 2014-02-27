<?php
class script2
{
	function testFork(){
		
		$time = microtime(true);
		$worker_processes = 5;
		$child_processes = array();

		for ($i = 0; $i < $worker_processes; $i++) {

		    $child_pid = pcntl_fork();
		    
		    if ($child_pid == -1) {
		        die ("Can't fork process");
		    } elseif ($child_pid) {
		        print "Parent, created child: $child_pid\n";
		        $child_processes[] = $child_pid;     
		    
		        # В данный момент все процессы отфоркнуты, можно начать ожидание
		        if ($i == ( $worker_processes -1 ) ) {
		            foreach ($child_processes as $process_pid) {
		                # Ждем завершение заданного дочернего процесса
		                $status = 0;
		                pcntl_waitpid($process_pid, $status); 
		            }
		        }
		    } else {
		        print "Child $i\n";
		        sleep(20);
		    
		        # Если здесь не будет exit, то foreach заработает и здесь
		        exit(0);
		    }

		}
		var_dump(microtime(true) - $time);
	}
}
$script = new script2();
$script->testFork();