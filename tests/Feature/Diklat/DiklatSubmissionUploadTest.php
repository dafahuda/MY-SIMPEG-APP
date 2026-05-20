<?php

namespace Tests\Feature\Diklat;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiklatSubmissionUploadTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_upload_helper_stores_fake_evidence_without_touching_real_storage(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $upload = $this->fakeSubmissionEvidenceUpload();
        $path = $upload->store('bukti_diklat', 'public');

        $fixture['submissionInScope']->update([
            'file_bukti' => $path,
        ]);

        Storage::disk('public')->assertExists($path);
        $this->assertSame($path, $fixture['submissionInScope']->fresh()->file_bukti);
    }
}
