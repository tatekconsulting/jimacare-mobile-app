<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Role;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$roles = Role::with('faqs')
		    ->where('active', true)
		    ->get()
	    ;
	    
	    // Update FAQ content to reflect current platform policies
	    foreach ($roles as $role) {
	        // Filter out fee-related FAQs and self-employment FAQs
	        $role->faqs = $role->faqs->filter(function($faq) {
	            // Filter out fee-related FAQs (check for various title formats)
	            $isFeeFaq = stripos($faq->title, 'How much does Jimacare charge') !== false ||
	                       stripos($faq->title, 'How much does Jimacare charges') !== false ||
	                       (stripos($faq->title, 'fee') !== false && stripos($faq->title, 'charge') !== false);
	            
	            // Filter out self-employment FAQs
	            $isSelfEmployFaq = stripos($faq->title, 'Must I be self employ') !== false ||
	                              stripos($faq->title, 'self employ') !== false;
	            
	            // Also filter by description content
	            $hasFeeDescription = stripos($faq->desc, 'JimaCare no longer collects fees or processes payments') !== false ||
	                                stripos($faq->desc, 'no longer collects fees') !== false;
	            
	            // Filter out FAQs with the support team employment answer
	            $hasSupportTeamAnswer = stripos($faq->desc, 'Please contact our support team for current employment arrangements and requirements') !== false ||
	                                   stripos($faq->desc, 'contact our support team for current employment') !== false;
	            
	            return !$isFeeFaq && !$isSelfEmployFaq && !$hasFeeDescription && !$hasSupportTeamAnswer;
	        });
	        
	        foreach ($role->faqs as $faq) {
	            // Remove self-employment mentions from FAQ descriptions
	            $faq->desc = str_ireplace(
	                ['self-employed', 'self employed', 'self-employment', 'self employment'],
	                '',
	                $faq->desc
	            );
	            // Clean up any double spaces or line breaks
	            $faq->desc = preg_replace('/\s+/', ' ', trim($faq->desc));
	            
	            // Update payment collection FAQs
	            if (stripos($faq->title, 'How do I pay') !== false || 
	                stripos($faq->title, 'How do you pay') !== false) {
	                if ($role->slug == 'childminder') {
	                    $faq->desc = 'Payment arrangements are made by our JimaCare Payment Team.';
	                } else {
	                    $faq->desc = 'Payment arrangements are made by our JimaCare Payment Team.';
	                }
	            }
	            
	            // Remove "You can make agreement on price with carer you prefer" text
	            if (stripos($faq->desc, 'You can make agreement on price with carer you prefer') !== false) {
	                $faq->desc = str_ireplace(
	                    'You can make agreement on price with carer you prefer.',
	                    '',
	                    $faq->desc
	                );
	                // Also remove any variations
	                $faq->desc = str_ireplace(
	                    'You can make agreement on price with carer you prefer',
	                    '',
	                    $faq->desc
	                );
	                // Clean up any double spaces or line breaks
	                $faq->desc = preg_replace('/\s+/', ' ', trim($faq->desc));
	            }
	            
	            // Remove "All payments made on this platform are secured and encrypted" text
	            if (stripos($faq->desc, 'All payments made on this platform are secured and encrypted') !== false) {
	                $faq->desc = str_ireplace(
	                    'All payments made on this platform are secured and encrypted.',
	                    '',
	                    $faq->desc
	                );
	                // Also remove any variations
	                $faq->desc = str_ireplace(
	                    'All payments made on this platform are secured and encrypted',
	                    '',
	                    $faq->desc
	                );
	                // Clean up any double spaces or line breaks
	                $faq->desc = preg_replace('/\s+/', ' ', trim($faq->desc));
	            }
	            
	            // Update "What do we do" FAQ for carers
	            if ($role->slug == 'carer' && 
	                (stripos($faq->title, 'What do we do') !== false || 
	                 stripos($faq->title, 'What do you do') !== false)) {
	                $faq->desc = 'JimaCare advertises your profile to clients and there is no sign-up fee. JimaCare allows you to apply for jobs on the platform which are updated daily. JimaCare organises your carer insurance as long as you transact on the site. Payment arrangements are made by our JimaCare Payment Team.';
	            }
	            
	            // Update payment guarantee mentions
	            if (stripos($faq->desc, 'guarantees your payment') !== false) {
	                $faq->desc = str_ireplace(
	                    'JimaCare guarantees your payment from your clients to you',
	                    'Payment arrangements are made by our JimaCare Payment Team',
	                    $faq->desc
	                );
	            }
	            
	            // Update mentions of platform payment processing
	            if (stripos($faq->desc, 'through Jimacare platform') !== false || 
	                stripos($faq->desc, 'through the platform') !== false) {
	                $faq->desc = str_ireplace(
	                    ['through Jimacare platform', 'through the platform', 'through Jimacare'],
	                    'directly with the service provider',
	                    $faq->desc
	                );
	            }
	            
	            // Update "Who will pay my traveling expenses" for carers
	            if ($role->slug == 'carer' && 
	                stripos($faq->title, 'traveling expenses') !== false) {
	                $faq->desc = 'You are responsible for negotiating your terms with the client. Your travelling expenses can be added to your hourly or weekly rate or you can make an agreement with the client to pay separately. Payment arrangements are made by our JimaCare Payment Team.';
	                // Remove self-employment references
	                $faq->desc = str_ireplace(
	                    ['self-employed', 'self employed'],
	                    '',
	                    $faq->desc
	                );
	                $faq->desc = preg_replace('/\s+/', ' ', trim($faq->desc));
	            }
	        }
	    }
	    
        return view('app.pages.faqs', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function show(Faq $faq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function edit(Faq $faq)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faq $faq)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        //
    }
}
