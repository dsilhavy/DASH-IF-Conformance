<?php

namespace DASHIF;

class ModuleHbbTVDVB extends ModuleInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->name = "HbbTV_DVB";

        $this->HbbTvEnabled = false;
        $this->DVBEnabled = false;
    }

    protected function addCLIArguments()
    {
        global $argumentParser;
        $argumentParser->addOption("hbbtv", "H", "hbbtv", "Enable HBBTV checking");
        $argumentParser->addOption("dvb", "D", "dvb", "Enable DVB checking");
    }

    public function handleArguments()
    {
        global $argumentParser;
        if ($argumentParser->getOption("hbbtv")) {
            $this->enabled = true;
            $this->HbbTvEnabled = true;
        }
        if ($argumentParser->getOption("dvb")) {
            $this->enabled = true;
            $this->DVBEnabled = true;
        }
    }

    public function hookBeforeMPD()
    {
        parent::hookBeforeMPD();
        $this->moveScripts();
        include_once 'impl/beforeMPD.php';
    }

    public function hookMPD()
    {
        parent::hookMPD();
        $this->profileSpecificMediaTypesReport();
        $this->crossProfileCheck();

        if ($this->DVBEnabled) {
            $this->dvbMPDValidator();
        //DVB_mpd_anchor_check($mpdreport);
        }

        if ($this->HbbTVEnabled) {
        //HbbTV_mpdvalidator($mpdreport);
        }
    }

    private function profileSpecificMediaTypesReport()
    {
        include 'impl/profileSpecificMediaTypesReport.php';
    }

    private function crossProfileCheck()
    {
        include 'impl/crossProfileCheck.php';
    }

    private function dvbMPDValidator()
    {
        include 'impl/dvbMPDValidator.php';
    }

    private function tlsBitrateCheck()
    {
        include 'impl/tlsBitrateCheck.php';
    }

    private function checkDVBValidRelative()
    {
        include 'impl/checkDVBValidRelative.php';
    }

    private function dvbMetricReporting()
    {
        include 'impl/dvbMetricReporting.php';
    }

    public function hookBeforeRepresentation()
    {
        HbbTV_DVB_flags();
        return is_subtitle();
    }

    public function hookRepresentation()
    {
        return RepresentationValidation_HbbTV_DVB();
    }

    public function hookBeforeAdaptationSet()
    {
        return add_remove_images('REMOVE');
    }

    public function hookAdaptationSet()
    {
        return CrossValidation_HbbTV_DVB();
    }

    private function moveScripts()
    {
        global $session_dir, $bitrate_script, $segment_duration_script;

        copy(dirname(__FILE__) . "/$bitrate_script", "$session_dir/$bitrate_script");
        chmod("$session_dir/$bitrate_script", 0777);
        copy(dirname(__FILE__) . "/$segment_duration_script", "$session_dir/$segment_duration_script");
        chmod("$session_dir/$segment_duration_script", 0777);
    }
}

$modules[] = new ModuleHbbTVDVB();
