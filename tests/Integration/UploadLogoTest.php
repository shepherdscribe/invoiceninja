<?php

namespace Tests\Integration;

use App\Events\Invoice\InvoiceWasCreated;
use App\Events\Invoice\InvoiceWasUpdated;
use App\Events\Payment\PaymentWasCreated;
use App\Jobs\Invoice\MarkInvoicePaid;
use App\Jobs\Util\UploadFile;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Company;
use App\Models\CompanyLedger;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Utils\Traits\MakesHash;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\MockAccountData;
use Tests\TestCase;

/**
 * @test
 */
class UploadLogoTest extends TestCase
{
    use MockAccountData;
    use DatabaseTransactions;
    use MakesHash;

    public function setUp() :void
    {
        parent::setUp();

        $this->makeTestData();
    }


    public function testLogoUploadWorks()
    {

        Storage::fake('avatars');

        $data = [
            'logo' => UploadedFile::fake()->image('avatar.jpg'),
            'name' => 'TestCompany'
        ];

        $response = $this->withHeaders([
                'X-API-SECRET' => config('ninja.api_secret'),
                'X-API-TOKEN' => $this->token,
            ])->put('/api/v1/companies/'.$this->encodePrimaryKey($this->company->id), $data);
        
        $response->assertStatus(200);
        $acc = $response->json();
        $logo = $acc['data']['logo'];                

        $logo_file = Storage::url($logo);

        $this->assertNotNull($logo_file);
    }



}