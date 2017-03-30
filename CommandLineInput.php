<?php


namespace CommandLineInput;

/**
 * This concrete class implements the standard notation described in the implemented interface.
 *
 */
class CommandLineInput implements CommandLineInputInterface
{

    /**
     * array of key => boolean
     * flags are one letter short options that don't accept values
     */
    private $flags;

    /**
     * array of key => value (=false by default)
     */
    private $options;

    /**
     * index starts at 1, parameters are registered in order from left to right
     * array of index => value
     * Parameters are defined while parsing the command line.
     */
    private $parameters;

    private $argv;
    private $isPrepared;


    public function __construct(array $argv)
    {
        $this->flags = [];
        $this->options = [];
        $this->parameters = [];

        //
        $this->argv = $argv;
        $this->isPrepared = false;
    }


    public static function create(array $argv)
    {
        return new static($argv);
    }


    /**
     * @return CommandLineInput
     */
    public function addFlag($name)
    {
        $this->flags[$name] = false;
        return $this;
    }

    /**
     * @return CommandLineInput
     */
    public function addOption($name)
    {
        $this->options[$name] = false;
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getFlagValue($flagName, $default = null)
    {
        $this->prepare();
        if (array_key_exists($flagName, $this->flags)) {
            return $this->flags[$flagName];
        }
        return $default;
    }

    public function getOptionValue($optionName, $default = null)
    {
        $this->prepare();
        if (array_key_exists($optionName, $this->options)) {
            return $this->options[$optionName];
        }
        return $default;
    }

    public function getParameter($index, $default = null)
    {
        $this->prepare();
        if (array_key_exists($index, $this->parameters)) {
            return $this->parameters[$index];
        }
        return $default;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function error($type, $param = null, $param2 = null)
    {
        $msg = "";
        switch ($type) {
            case 'flagNotFound':
                $msg = "Flag not found: $param";
                break;
            case 'combinedFlagNotFound':
                $msg = "Flag not found: $param (in combined flags -$param2)";
                break;
            case 'unknownShortOptionType':
                $msg = "Unknown short option type: -$param";
                break;
//            case 'invalidLongOptionSyntax':
//                $msg = "Invalid long option syntax: --$param, a long option should contain the equal symbol, with no spaces around";
//                break;
            case 'longOptionNotFound':
                $msg = "Long option not found: $param";
                break;
            case 'shortOptionNotFound':
                $msg = "Short option not found: $param";
                break;
            case 'longFlagNotFound':
                $msg = "Long flag not found: $param";
                break;
            default:
                break;
        }
        $this->writeError($msg);
    }

    protected function writeError($msg)
    {
        echo $msg . PHP_EOL;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function prepare()
    {
        if (false === $this->isPrepared) {
            $this->isPrepared = true;
            $this->prepareOptions($this->argv);
        }
    }


    private function prepareOptions(array $argv)
    {
        // drop program name
        array_shift($argv);
        $paramIndex = 1;
        foreach ($argv as $v) {
            if (0 === strpos($v, '-')) {
                if (0 === strpos($v, '--')) {
                    // long option or long flag
                    $option = ltrim($v, '-');

                    $p = explode('=', $option, 2);
                    if (2 === count($p)) {
                        // long option
                        $optionName = $p[0];
                        $optionValue = $p[1];
                        if (array_key_exists($optionName, $this->options)) {
                            $this->options[$optionName] = $optionValue;
                        } else {
                            $this->error("longOptionNotFound", $optionName);
                        }
                    } else {
                        // long flag
                        if (array_key_exists($option, $this->flags)) {
                            $this->flags[$option] = true;
                        } else {
                            $this->error("longFlagNotFound", $option);
                        }
                    }
                } else {

                    // short option or short flag or combined short flags
                    $option = ltrim($v, '-');

                    $p = explode('=', $option, 2);
                    if (2 === count($p)) {
                        // short option
                        $optionName = $p[0];
                        $optionValue = $p[1];
                        if (array_key_exists($optionName, $this->options)) {
                            $this->options[$optionName] = $optionValue;
                        } else {
                            $this->error("shortOptionNotFound", $optionName);
                        }
                    } else {
                        // short flag or combined one letter flags
                        $len = strlen($option);
                        if (1 === $len) {
                            // short flag
                            if (array_key_exists($option, $this->flags)) {
                                $this->flags[$option] = true;
                            } else {
                                $this->error("flagNotFound", $option);
                            }
                        } elseif ($len > 1) {
                            // assuming combined flags
                            $chars = str_split($option);
                            foreach ($chars as $char) {
                                if (array_key_exists($char, $this->flags)) {
                                    $this->flags[$char] = true;
                                } else {
                                    $this->error("combinedFlagNotFound", $char, $option);
                                }
                            }
                        } else {
                            $this->error("unknownShortOptionType", $option);
                        }
                    }
                }
            } else {
                $this->parameters[$paramIndex++] = $v;
            }
        }
    }

}