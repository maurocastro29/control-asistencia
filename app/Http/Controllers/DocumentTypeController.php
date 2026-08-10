<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocumentTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:document-types.view')
            ->only(['index', 'show']);

        $this->middleware('permission:document-types.create')
            ->only(['create', 'store']);

        $this->middleware('permission:document-types.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:document-types.delete')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::orderBy('name')->paginate(10);
        return view('document-types.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('document-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentTypeRequest $request)
    {
        DocumentType::create($request->validated());
        return redirect()
            ->route('document-types.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentType $documentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentType $documentType)
    {
        return view('document-types.edit', compact('documentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType)
    {
        $documentType->update($request->validated());

        return redirect()
            ->route('document-types.index')
            ->with('success', 'Tipo de documento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();
        return redirect()
            ->route('document-types.index')
            ->with('success', 'Tipo de documento eliminado correctamente.');
    }
}