<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class GenerateModuleTests extends Command
{
    protected $signature = 'module:generate-tests';
    protected $description = 'Generate intelligent test cases for modules using OpenAI';

    public function handle()
    {
        $modulesPath = base_path('Modules');

        foreach (File::directories($modulesPath) as $modulePath) {
            $moduleName = basename($modulePath);
            $controllersPath = $modulePath . '/Http/Controllers';

            if (!File::exists($controllersPath)) continue;

            foreach (File::allFiles($controllersPath) as $file) {
                $code = File::get($file);
                $methods = $this->getMethodsFromCode($code);

                foreach ($methods as $method) {
                    $this->info("Generating test for method: {$method['name']}");

                    $testCode = $this->generateTestForMethod($method, $code);
                    $this->storeTestFile($modulePath, $method['name'], $testCode);
                }
            }
        }

        $this->info("All done!");
    }

    /**
     * Extract methods from the controller code
     *
     * @param string $code
     * @return array
     */
    private function getMethodsFromCode($code)
    {
        preg_match_all('/public function (\w+)\(.*\)/', $code, $matches);

        $methods = [];
        foreach ($matches[1] as $methodName) {
            $methods[] = [
                'name' => $methodName,
                'signature' => $this->getMethodSignature($code, $methodName),
            ];
        }

        return $methods;
    }

    /**
     * Get the full method signature from the code
     *
     * @param string $code
     * @param string $methodName
     * @return string
     */
    private function getMethodSignature($code, $methodName)
    {
        preg_match('/public function ' . $methodName . '\(.*\)\s*{/', $code, $matches);
        return $matches[0] ?? '';
    }

    /**
     * Generate test code for a given method
     *
     * @param array $method
     * @param string $controllerCode
     * @return string
     */
    private function generateTestForMethod($method, $controllerCode)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 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',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a Laravel developer who writes PestPHP unit tests based on method functionality.'],
                ['role' => 'user', 'content' => "Write a Pest test for this Laravel controller method. Here's the method signature and logic:\n\n{$method['signature']}\n\nController code:\n\n{$controllerCode}"]
            ],
            'temperature' => 0.5,
        ]);
        
        return $response->json('choices.0.message.content');
    }

    /**
     * Store the generated test code into a file
     *
     * @param string $modulePath
     * @param string $methodName
     * @param string $testCode
     * @return void
     */
    private function storeTestFile($modulePath, $methodName, $testCode)
    {
        $testPath = $modulePath . '/Tests/Feature';
        File::ensureDirectoryExists($testPath);

        $testFileName = $testPath . '/' . ucfirst($methodName) . 'Test.php';
        File::put($testFileName, $testCode);

        $this->info("✅ Test generated: " . $testFileName);
    }
}
