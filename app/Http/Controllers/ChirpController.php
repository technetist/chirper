<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChirpRequest;
use App\Models\Chirp;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        return Inertia::render('Chirps/Index', [
            'paginated_chirps' => Chirp::with('user:id,name')->latest()->paginate(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ChirpRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ChirpRequest $request): Application|RedirectResponse|Redirector
    {
        $request->user()->chirps()->create($request->only('message'));

        return redirect(route('chirps.index'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ChirpRequest $request
     * @param \App\Models\Chirp               $chirp
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ChirpRequest $request, Chirp $chirp): Application|RedirectResponse|Redirector
    {
        try {
            $this->authorize('update', $chirp);
        } catch (AuthorizationException $exception) {
            return redirect(route('chirps.index'))->withErrors(['authorization' => $exception->getMessage()]);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $chirp->update($validated);

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Chirp $chirp
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Chirp $chirp): Application|RedirectResponse|Redirector
    {
        try {
            $this->authorize('delete', $chirp);
        } catch (AuthorizationException $exception) {
            return redirect(route('chirps.index'))->withErrors(['authorization' => $exception->getMessage()]);
        }

        $chirp->delete();

        return redirect(route('chirps.index'));
    }
}
