<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LandingController extends Controller
{
    /**
     * Search for books using Python script
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'q' => 'required|string|min:1',
                'rank' => 'required|integer|min:1|max:100',
            ]);

            // Configure and run Python process
            $process = new Process([
                'python3',
                base_path('public/query.py'),
                base_path('public/indexdb'),
                $validated['rank'],
                $validated['q']
            ]);
            
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Process the Python script output
            $books = $this->processBookData($process->getOutput());

            return response()->json($books);

        } catch (ProcessFailedException $e) {
            return response()->json([
                'error' => 'Search process failed',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process the Python script output and format book data
     *
     * @param string $output
     * @return array
     */
    private function processBookData(string $output): array
    {
        $lines = array_filter(
            explode("\n", $output),
            fn($line) => str_starts_with(trim($line), '{')
        );

        return array_map(function ($book) {
            $bookData = json_decode($book, true);
            
            if (!$bookData) {
                return null;
            }

            return $this->formatBookCard($bookData);
        }, $lines);
    }

    /**
     * Format book data into HTML card
     *
     * @param array $book
     * @return string
     */
    private function formatBookCard(array $book): string
    {
        $baseUrl = 'http://books.toscrape.com';
        
        return <<<HTML
        <div class="col-lg-5">
            <div class="card mb-2 hover:shadow-lg transition-shadow duration-200">
                <div class="flex">
                    <div class="img-square-wrapper">
                        <img src="{$baseUrl}/{$book['image']}" 
                             alt="{$book['title']}"
                             class="object-cover h-48 w-32">
                    </div>
                    <div class="card-body p-4">
                        <h6 class="card-title font-medium mb-2">
                            <a href="{$baseUrl}/catalogue/{$book['url']}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 transition-colors">
                                {$book['title']}
                            </a>
                        </h6>
                        <p class="card-text text-green-600">
                            Price: {$book['price']}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }
}