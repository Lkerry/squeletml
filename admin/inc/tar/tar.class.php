<?php
/**
 * @package PHPClassCollection
 * @subpackage Tar
 * @link http://php-classes.sourceforge.net/ PHP Class Collection
 * @author Dennis Wronka <reptiler@users.sourceforge.net>
 */
/**
 * @package PHPClassCollection
 * @subpackage Tar
 * @link http://php-classes.sourceforge.net/ PHP Class Collection
 * @author Dennis Wronka <reptiler@users.sourceforge.net>
 * @version 1.1.3
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL 2.1
 */
class tar
{
	/**
	 * The name of the tar-file to create.
	 *
	 * @var string
	 */
	private $filename;
	/**
	 * The list of files to add to the archive.
	 *
	 * @var array
	 */
	private $filelist=array();

	/**
	 * Constructor
	 *
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		$this->filename=$filename;
	}

	/**
	 * Add a file.
	 *
	 * @param string $filename
	 */
	public function add($filename)
	{
		if ((file_exists($filename)) && (is_readable($filename)))
		{
			$this->filelist[]=$filename;
		}
	}

	/**
	 * Set the filelist.
	 *
	 * @param array $filelist
	 */
	public function setfilelist($filelist)
	{
		$this->filelist=$filelist;
	}

	/**
	 * Write the tar-file.
	 *
	 * @return bool
	 */
	public function write()
	{
		sort($this->filelist);
		$tarfile=@fopen($this->filename,'w');
		if ($tarfile==false)
		{
			return false;
		}
		for ($x=0;$x<count($this->filelist);$x++)
		{
			$filename=$this->filelist[$x];
			if ((is_dir($this->filelist[$x])) && (substr($this->filelist[$x],-1)!='/'))
			{
				$filename.='/';
			}
			while (strlen($filename)<100)
			{
				$filename.=chr(0);
			}
			$permissions=sprintf('%o',fileperms($this->filelist[$x])).chr(0);
			while (strlen($permissions)<8)
			{
				$permissions='0'.$permissions;
			}
			$userid=sprintf('%o',fileowner($this->filelist[$x])).chr(0);
			while (strlen($userid)<8)
			{
				$userid='0'.$userid;
			}
			$groupid=sprintf('%o',filegroup($this->filelist[$x])).chr(0);
			while (strlen($groupid)<8)
			{
				$groupid='0'.$groupid;
			}
			if (is_dir($this->filelist[$x]))
			{
				$filesize='0'.chr(0);
			}
			else
			{
				$filesize=sprintf('%o',filesize($this->filelist[$x])).chr(0);
			}
			while (strlen($filesize)<12)
			{
				$filesize='0'.$filesize;
			}
			$modtime=sprintf('%o',filectime($this->filelist[$x])).chr(0);
			$checksum='        ';
			if (is_dir($this->filelist[$x]))
			{
				$indicator=5;
			}
			else
			{
				$indicator=0;
			}
			$linkname='';
			while (strlen($linkname)<100)
			{
				$linkname.=chr(0);
			}
			$ustar='ustar  '.chr(0);
			if (function_exists('posix_getpwuid'))
			{
				$user=posix_getpwuid(octdec($userid));
				$user=$user['name'];
			}
			else
			{
				$user='';
			}
			while (strlen($user)<32)
			{
				$user.=chr(0);
			}
			if (function_exists('posix_getgrgid'))
			{
				$group=posix_getgrgid(octdec($groupid));
				$group=$group['name'];
			}
			else
			{
				$group='';
			}
			while (strlen($group)<32)
			{
				$group.=chr(0);
			}
			$devmajor='';
			while (strlen($devmajor)<8)
			{
				$devmajor.=chr(0);
			}
			$devminor='';
			while (strlen($devminor)<8)
			{
				$devminor.=chr(0);
			}
			$prefix='';
			while (strlen($prefix)<155)
			{
				$prefix.=chr(0);
			}
			$header=$filename.$permissions.$userid.$groupid.$filesize.$modtime.$checksum.$indicator.$linkname.$ustar.$user.$group.$devmajor.$devminor.$prefix;
			while (strlen($header)<512)
			{
				$header.=chr(0);
			}
			$checksum=0;
			for ($y=0;$y<strlen($header);$y++)
			{
				$checksum+=ord($header[$y]);
			}
			$checksum=sprintf('%o',$checksum).chr(0).' ';
			while (strlen($checksum)<8)
			{
				$checksum='0'.$checksum;
			}
			$header=$filename.$permissions.$userid.$groupid.$filesize.$modtime.$checksum.$indicator.$linkname.$ustar.$user.$group.$devmajor.$devminor.$prefix;
			while (strlen($header)<512)
			{
				$header.=chr(0);
			}
			fwrite($tarfile,$header);
			if ($indicator==0)
			{
				if (filesize($this->filelist[$x])>0)
				{
					$contentfile=fopen($this->filelist[$x],'r');
					$data=fread($contentfile,filesize($this->filelist[$x]));
					fclose($contentfile);
				}
				else
				{
					$data='';
				}
				while (strlen($data)%512!=0)
				{
					$data.=chr(0);
				}
				fwrite($tarfile,$data);
			}
		}
		fclose($tarfile);
		return true;
	}
}
?>
