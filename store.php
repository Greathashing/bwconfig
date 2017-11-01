<?php
$ip = $argv[1];
$port = $argv[2];
echo $ip . " $port "."\n";

$ip_no_dots = str_replace(".","",$ip);

$conf_default= <<< EOF
{
"HttpPort":"80",
"pool1":"stratum+tcp://192.168.209.1:$port|xxx|123123",
"pool2":"stratum+tcp://scrypt.usa.nicehash.com:3333|xxx|123123",
"pool3":"stratum+tcp://us.litecoinpool.org:3333|xxx.$ip_no_dots|x",
"failover-only":true,
"no-submit-stale":true,
"api-listen":true,
"api-port":"4028",
"api-allow":"W:0/0|W:10.0.0/24|W:127.0.0.1",
"volt":"2",
"defaultWebFolder":"/home/will/cg/web2",
"username":"admin",
"password":"bw.com",
"language":"ch",
"autoNet":true,
"ip":"$ip",
"mask":"255.255.255.0",
"gateway":"192.168.210.1",
"dns":"8.8.8.8",
"chipNumber":"36",
"frequency":"684",
"autoFrequency":true,
"autoGetJobTimeOut":true,
"frequencySet":"384_30|450_30|480_30|540_30|576_30|600_30|612_30|625_30|636_30|648_30|660_30|672_30|684_29|700_28|720_28|744_28|756_28|768_28|800_28|912_28|1020_28",
"debug":true,
"packet":true,
"botelv":true,
"board_reset_waittime":"14",
"mcu_reset_waittime":"0",
"invalid_cnt":"30",
"scanwork_sleeptime":"4",
"board_reenable_waittime":"60",
"temp_threshold":"80",
"task_interval":"350",
"start_voltage":"6000",
"running_voltage1":"5650",
"running_voltage2":"5650",
"running_voltage3":"5650",
"fengru":"5000",
"fengchu":"5000"
}

EOF;

$interfaces= <<<EOF
auto lo
auto eth0
iface lo inet loopback
iface eth0 inet static
address $ip
netmask 255.255.255.0
gateway 192.168.210.1

EOF;

$resolv_conf= <<< EOF
nameserver 8.8.8.8

EOF;

$fp = fopen("temp_resolv","w");
fwrite($fp,$resolv_conf);
fclose($fp);

$fp = fopen("temp_interfaces","w");
fwrite($fp,$interfaces);
fclose($fp);

$fp = fopen("temp_conf_default","w");
fwrite($fp,$conf_default);
fclose($fp);

$connect = ssh2_connect("10.0.0.10");
//$connect = ssh2_connect($ip);
echo ssh2_auth_password ($connect, "root" , "bwcon" );
ssh2_scp_send ( $connect, "temp_resolv", "/etc/resolv.conf");
ssh2_scp_send ( $connect, "temp_interfaces", "/etc/network/interfaces");
ssh2_scp_send ( $connect, "temp_conf_default", "/usr/app/conf.default");
ssh2_exec($connect,"kill `pidof cgminer`");
ssh2_exec($connect,"reboot");


?>
