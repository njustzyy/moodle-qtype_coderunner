<?php
/**
 * Unit tests for coderunner's jobe sandbox class.
 * Need full internet connectivity to run this as it needs to
 * send jobs to jobe.com.
 *
 * @package    qtype
 * @subpackage coderunner
 * @copyright  2013 Richard Lobb, University of Canterbury
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->dirroot . '/question/type/coderunner/tests/coderunnertestcase.php');
require_once($CFG->dirroot . '/question/type/coderunner/Sandbox/jobesandbox.php');

// TODO: remove case sensitivity on languages
// TODO: add sandbox parameter handling.

class qtype_coderunner_jobesandbox_test extends qtype_coderunner_testcase {

    public function test_languages() {
        $sandbox = new JobeSandbox(); 
        $langObj = $sandbox->getLanguages();
        $this->assertEquals(0, $langObj->error);
        $langs = $langObj->languages;
        $this->assertTrue(in_array('python3', $langs, TRUE));
        $this->assertTrue(in_array('c', $langs, TRUE));
    }


    public function test_jobesandbox_python3_good() {
        // Test the jobe sandbox using the execute method of the base class
        // with a valid python3 program.
        $source = 'print("Hello sandbox!")';
        $sandbox = new JobeSandbox();
        $result = $sandbox->execute($source, 'python3', '');
        $this->assertEquals(Sandbox::OK, $result->error);
        $this->assertEquals(Sandbox::RESULT_SUCCESS2, $result->result);
        $this->assertEquals('', $result->stderr);
        $this->assertEquals('', $result->cmpinfo);
        $this->assertEquals("Hello sandbox!\n", $result->output);
        $sandbox->close();
    }


    // Test the jobe sandbox using the execute method of the base class
    // with a syntactically invalid python3 program.
    public function test_jobesandbox_python3_bad() {
        $source = "print('Hello sandbox!'):\n";
        $sandbox = new JobeSandbox();
        $result = $sandbox->execute($source, 'python3', '');
        $this->assertEquals(Sandbox::RESULT_COMPILATION_ERROR, $result->result);
        $sandbox->close();
    }


    public function test_jobesandbox_python3_timeout() {
        // Test the jobe sandbox using the execute method of the base class
        // with a python3 program that loops.
        $source = "while 1: pass\n";
        $sandbox = new JobeSandbox();
        $result = $sandbox->execute($source, 'python3', '');
        $this->assertEquals(Sandbox::RESULT_TIME_LIMIT, $result->result);
        $this->assertEquals('', $result->output);
        $this->assertEquals('', $result->stderr);
        $this->assertEquals('', $result->cmpinfo);
        $sandbox->close();
    }


    // Test the jobe sandbox with a syntactically bad C program
    public function test_jobe_sandbox_bad_C() {
        $sandbox = new JobeSandbox();
        $code = "#include <stdio.h>\nint main(): {\n    printf(\"Hello sandbox\");\n    return 0;\n}\n";
        $result = $sandbox->execute($code, 'c', NULL);
        $this->assertEquals(Sandbox::RESULT_COMPILATION_ERROR, $result->result);
        $this->assertTrue(strpos($result->cmpinfo, 'error:') !== FALSE);
        $sandbox->close();
    }

    // Test the jobe sandbox with a valid C program
    public function test_jobe_sandbox_ok_C() {
        $sandbox = new JobeSandbox();
        $code = "#include <stdio.h>\nint main() {\n    printf(\"Hello sandbox\\n\");\n    return 0;\n}\n";
        $result = $sandbox->execute($code, 'c', NULL);
        $this->assertEquals(Sandbox::RESULT_SUCCESS2, $result->result);
        $this->assertEquals("Hello sandbox\n", $result->output);
        $this->assertEquals(0, $result->signal);
        $this->assertEquals('', $result->cmpinfo);
        $sandbox->close();
    }


/*
    // Test the Ideone sandbox will not allow opening, writing and reading in /tmp
    public function test_jobe_sandbox_fileio_bad() {
        $sandbox = new JobeSandbox();
        $code =
"import os
f = open('/tmp/junk', 'w')
f.write('stuff')
f.close()
f = open('/tmp/junk')
print(f.read())
f.close()
";
        $result = $sandbox->execute($code, 'python3', NULL);
        $this->assertEquals(Sandbox::RESULT_RUNTIME_ERROR, $result->result);
        $sandbox->close();
    }
*/
}

?>