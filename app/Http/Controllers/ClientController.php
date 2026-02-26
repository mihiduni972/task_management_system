<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Display a paginated list of clients.
     */
    public function index()
    {
        // Retrieve latest clients with pagination
        $clients = Client::latest()->paginate(7);

        return view('frontend.admin.clients.index', compact('clients'));
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        // Create client using only allowed fields
        $client = Client::create(
            $request->only('name', 'company', 'email', 'phone', 'address', 'status')
        );

        return response()->json([
            'success' => true, // Makes it easier to handle response on frontend
            'message' => 'Client added successfully!',
            'data'    => $client
        ]);
    }

    /**
     * Update an existing client.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        // Find client and update selected fields
        $client = Client::findOrFail($id);
        $client->update(
            $request->only('name', 'company', 'email', 'phone', 'address', 'status')
        );

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully!',
            'data'    => $client
        ]);
    }

    /**
     * Return client data for editing (JSON response).
     */
    public function edit(Client $client)
    {
        return response()->json($client);
    }

    /**
     * Display a single client (JSON response).
     */
    public function show(Client $client)
    {
        return response()->json($client);
    }

    /**
     * Delete a client.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully!'
        ]);
    }
}