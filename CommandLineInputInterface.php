<?php


namespace CommandLineInput;

/**
 * This object is an api to access command line options and parameters.
 * What's an option and what's a parameter might be redefined on a per concrete class basis.
 *
 * But if otherwise not specified, here is the conception that should prevail.
 *
 *
 *
 * Options
 * =============
 * An option is one of two types:
 *
 * - flag
 * - option with value
 *
 *
 * A flag is an option without value.
 * To differentiate between both types, we use the equal symbol (=) for options with value.
 *
 * An equal symbol is used to separate the key from a value.
 * There is no space around the equal symbol.
 *
 * Options with values and flags are prefixed with one or two dashes, depending on
 * the length of the flag name or option name.
 *
 * A one letter flag or option would be prefixed with one dash,
 * while a longer flag or option would be prefixed by two dashes.
 *
 * It's also possible to combine multiple one letter flags in one.
 * For instance, -vf is equivalent to -v -f.
 *
 *
 * An option value can be surrounded with single or double quotes to enclose
 * some special chars (like space for instance).
 * For instance, --my-option="some value"
 *
 *
 * By default, the value of a flag or option with value is false if not set.
 * With flags, the value becomes true if set.
 * For options with value, the value becomes the value set in the command line.
 *
 *
 * Parameters
 * =============
 * A parameter is any string in the command line that doesn't start with a dash.
 *
 * So for instance, given the following command line:
 *
 *      php -f myprogram.php makecoffee -v --sugars=2 viennois
 *
 * The parameters are: makecoffee and viennois.
 * They should be accessible by their number, starting with 1 (not 0).
 * So parameter 1 would be makecoffee, and viennois would be parameter 2.
 *
 * Note that a parameter can be a command name (like makecoffee in this case) or even a value like "my car is green",
 * but that's your responsibility to differentiate the role of the parameter in your context.
 *
 */
interface CommandLineInputInterface
{
    /**
     * returns the flag value (bool), or the $default if the flag was not defined.
     */
    public function getFlagValue($flagName, $default = null);


    /**
     * returns the option value, or the $default if the option was not defined.
     * False is returned if the option is defined but not present.
     */
    public function getOptionValue($optionName, $default = null);

    /**
     * returns the parameter indexed by the given $index.
     * False is returned if the parameter is defined but not found.
     * The $default is returned if the parameter is not defined at all.
     */
    public function getParameter($index, $default = null);
}