<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibArticle;
use App\Models\Library\LibAcctCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = LibArticle::select(
            'id',
            'article_code',
            'article_desc',
            'ac_id_ppe',
            'ac_id_ics',
            'eul_yr'
        )
        ->orderBy('article_desc')
        ->get();

        return response()->json($articles, 200);
    }

 public function computeEUL(Request $request, $id)
    {
        try {
            $article = LibArticle::find($id);

            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found.',
                ], 404);
            }

            $unitCost = floatval($request->query('unit_cost', 0));

            // Fetch linked account codes safely
            $ppe = $article->ac_id_ppe ? LibAcctCode::find($article->ac_id_ppe) : null;
            $ics = $article->ac_id_ics ? LibAcctCode::find($article->ac_id_ics) : null;

            // Default SPLV case (< ₱5,000)
            $data = [
                'type' => 'SPLV',
                'account_code' => $ics?->acct_code ?? null,
                'account_desc' => $ics?->acct_desc ?? null,
                'eul' => $article->eul_yr_splv ?? 0,
            ];

            // ₱5,000–₱49,999 range = SPHV
            if ($unitCost >= 5000 && $unitCost < 50000 && $ics) {
                $data = [
                    'type' => 'SPHV',
                    'account_code' => $ics->acct_code ?? null,
                    'account_desc' => $ics->acct_desc ?? null,
                    'eul' => $article->eul_yr_sphv ?? 0,
                ];
            }

            // ₱50,000+ range = PPE
            if ($unitCost >= 50000 && $ppe) {
                $data = [
                    'type' => 'PPE',
                    'account_code' => $ppe->acct_code ?? null,
                    'account_desc' => $ppe->acct_desc ?? null,
                    'eul' => $article->eul_yr ?? 0,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ EUL computation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error during EUL computation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}



