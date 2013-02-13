<?php

namespace Browscap;

/**
 * Represents a browser
 *
 * @todo Be able to use this in a loop to get all properties
 * @todo magic methods get and set
 *
 * @author Joshua Estes <Joshua@Estes.in>
 */
class Browser
{

    public $MasterParent            = true;
    public $LiteMode                = true;
    public $Parent                  = 'DefaultProperties';
    public $Comments                = 'DefaultProperties';
    public $Browser                 = 'DefaultProperties';
    public $Version                 = 0.0;
    public $MajorVer                = 0;
    public $MinorVer                = 0;
    public $Platform_Version        = 0.0;
    public $Alpha                   = false;
    public $Beta                    = false;
    public $Win16                   = false;
    public $Win32                   = false;
    public $Win64                   = false;
    public $Frames                  = false;
    public $IFrames                 = false;
    public $Tables                  = false;
    public $Cookies                 = false;
    public $BackgroundSunds         = false;
    public $JavaScript              = false;
    public $VBScript                = false;
    public $JavaApplets             = false;
    public $ActiveXControls         = false;
    public $isMobileDevice          = false;
    public $isSyndicationReader     = false;
    public $Crawler                 = false;
    public $CssVersion              = 0.0;
    public $AolVersion              = 0.0;
    public $RenderingEngine_Version = 0.0;

    public $AgentID;
    public $PropertyName;
    public $X_Parent;
    public $Platform;
    public $Platform_Description;
    public $Device_Name;
    public $Device_Maker;
    public $RenderingEngine_Name;
    public $RenderingEngine_Description;

    public function setData(array $data)
    {
        $this->AgentID                     = $data['AgentID'];
        $this->PropertyName                = $data['PropertyName'];
        $this->MasterParent                = $data['MasterParent'];
        $this->LiteMode                    = $data['LiteMode'];
        $this->Parent                      = $data['Parent'];
        $this->X_Parent                    = isset($data['X_Parent']) ? $data['X_Parent'] : $this->X_Parent;
        $this->Comments                    = isset($data['Comments']) ? $data['Comments'] : $this->Comments;
        $this->Browser                     = $data['Browser'];
        $this->Version                     = $data['Version'];
        $this->MajorVer                    = $data['MajorVer'];
        $this->MinorVer                    = isset($data['MinorVer']) ? $data['MinorVer'] : $this->MinorVer;
        $this->Platform                    = $data['Platform'];
        $this->Platform_Version            = $data['Platform_Version'];
        $this->Platform_Description        = $data['Platform_Description'];
        $this->Alpha                       = $data['Alpha'];
        $this->Beta                        = $data['Beta'];
        $this->Win16                       = $data['Win16'];
        $this->Win32                       = $data['Win32'];
        $this->Win64                       = $data['Win64'];
        $this->Frames                      = $data['Frames'];
        $this->IFrames                     = $data['IFrames'];
        $this->Tables                      = $data['Tables'];
        $this->Cookies                     = $data['Cookies'];
        $this->BackgroundSounds            = $data['BackgroundSounds'];
        $this->JavaScript                  = $data['JavaScript'];
        $this->VBScript                    = $data['VBScript'];
        $this->JavaApplets                 = $data['JavaApplets'];
        $this->ActiveXControls             = $data['ActiveXControls'];
        $this->isMobileDevice              = $data['isMobileDevice'];
        $this->isSyndicationReader         = $data['isSyndicationReader'];
        $this->Crawler                     = $data['Crawler'];
        $this->CssVersion                  = $data['CssVersion'];
        $this->AolVersion                  = $data['AolVersion'];
        $this->Device_Name                 = $data['Device_Name'];
        $this->Device_Maker                = $data['Device_Maker'];
        $this->RenderingEngine_Name        = $data['RenderingEngine_Name'];
        $this->RenderingEngine_Version     = $data['RenderingEngine_Version'];
        $this->RenderingEngine_Description = $data['RenderingEngine_Description'];
    }

    public function toArray()
    {
        $ref = new \ReflectionObject($this);
        $properties = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
        $arr = array();
        foreach ($properties as $prop) {
            $propName = $prop->name;
            $arr[$propName] = $this->$propName;
        }

        return $arr;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function toXml()
    {
    }

    public function toIni()
    {
        $ini = sprintf("[%s]\n",$this->PropertyName);
        foreach ($this->toArray() as $k => $v) {
            $ini .= sprintf("%s=%s\n",$k,(string)$v);
        }
        return $ini;
    }

}
