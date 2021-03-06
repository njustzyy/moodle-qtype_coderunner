// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This AMD module provides the functionality for the "Reset"
 * button that is shown just below the student answer field if the question is
 * defined to have preloaded text.
 * If clicked, the button reloads the student answer field with the original
 * preloaded text (after a Confirm dialogue, of course).
 *
 * @package    qtype
 * @subpackage coderunner
 * @copyright  Richard Lobb, 2016, The University of Canterbury
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['jquery'], function($) {

    /**
     * Initialise the Reset button.
     */
    function initResetButton(buttonId, answerId, reloadText, confirmText) {
        var resetButton = $('[id="' + buttonId + '"]'),
            studentAnswer = $('[id="' + answerId + '"]'),
            uiWrapper;

        resetButton.on("click", function() {
            if (window.behattesting || window.confirm(confirmText)) {
                uiWrapper = studentAnswer.data('current-ui-wrapper');
                if (uiWrapper && uiWrapper.uiInstance) {
                    // If the textarea has a UI wrapper, and it's active
                    uiWrapper.stop();
                    studentAnswer.val(reloadText);
                    uiWrapper.restart();
                } else {
                    studentAnswer.val(reloadText);
                }
            }
        });
    }

    return { "initResetButton": initResetButton };
});
