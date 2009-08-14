<?php
/**
 * @package PHPClassCollection
 * @subpackage UnTar
 * @link classes
 * @author Dennis Wronka <reptiler@users.sourceforge.net>
 */
/**
 * @package PHPClassCollection
 * @subpackage UnTar
 * @link classes
 * @author Dennis Wronka <reptiler@users.sourceforge.net>
 * @version 1.1
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL 2.1
 */
class untar
{
	/**
	 * The filename of the tar-archive.
	 *
	 * @var string
	 */
	private $filename;
	/**
	 * The list of files and directories in the archive.
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
		$this->createfilelist();
	}

	/**
	 * Create the list of files and directories.
	 *
	 * @return bool
	 */
	private function createfilelist()
	{
		$tarfile=@fopen($this->filename,'r');
		if ($tarfile==false)
		{
			return false;
		}
		$datainfo='';
		$data='';
		while (!feof($tarfile))
		{
			$readdata=fread($tarfile,512);
			if (substr($readdata,257,5)=='ustar')
			{
				$offset=ftell($tarfile);
				$filename='';
				$position=0;
				$filename=substr($readdata,0,strpos($readdata,chr(0)));
				$permissions=substr($readdata,100,8);
				$filesize=octdec(substr($readdata,124,12));
				$indicator=substr($readdata,156,1);
				$fileuser=substr($readdata,265,strpos($readdata,chr(0),265)-265);
				$filegroup=substr($readdata,297,strpos($readdata,chr(0),297)-297);
				if ($indicator==5)
				{
					$filetype='directory';
					$offset=-1;
				}
				else
				{
					$filetype='file';
				}
				$this->filelist[]=array('filename'=>$filename,'filetype'=>$filetype,'offset'=>$offset,'filesize'=>$filesize,'user'=>$fileuser,'group'=>$filegroup,'permissions'=>$permissions);
			}
		}
		fclose($tarfile);
		return true;
	}

	/**
	 * Get the list of files and directories.
	 *
	 * @return array
	 */
	public function getfilelist()
	{
		return $this->filelist;
	}

	/**
	 * Extract a file or directory.
	 *
	 * @param string $filename
	 * @return mixed
	 */
	public function extract($filename)
	{
		$found=false;
		for ($x=0;$x<count($this->filelist);$x++)
		{
			if (in_array($filename,$this->filelist[$x]))
			{
				$found=$x;
			}
		}
		if ($found===false)
		{
			return false;
		}
		if ($this->filelist[$found]['filetype']=='directory')
		{
			return true;
		}
		$tarfile=@fopen($this->filename,'r');
		if ($tarfile==false)
		{
			return false;
		}
		fseek($tarfile,$this->filelist[$found]['offset']);
		$data=fread($tarfile,$this->filelist[$found]['filesize']);
		fclose($tarfile);
		return $data;
	}
}
?>