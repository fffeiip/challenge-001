<?php
use PHPUnit\Framework\TestCase;
use Core\PDFService;
use App\Models\User;

class PDFServiceTest extends TestCase
{
    public function testListPDFReturnsPDFBytes()
    {
        // Create a dummy user
        $user = new User();
        $user->username = 'tester';

        // Capture the PDF output
        ob_start();
        PDFService::list(
            'test.pdf',
            'Test Report',
            ['Col1','Col2'],
            [['A','B'], ['C','D']],
            $user
        );
        $pdf = ob_get_clean();

        // PDF files always start with '%PDF'
        $this->assertStringStartsWith('%PDF', $pdf, 'PDF output should start with %PDF');
        // And contain our title somewhere
        $this->assertStringContainsString('Test Report', $pdf, 'PDF should contain the title');
    }
}
