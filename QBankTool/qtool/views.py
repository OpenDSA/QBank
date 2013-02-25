# Create your views here.

from django.shortcuts import render_to_response, get_object_or_404
from django.core.context_processors import csrf
from django.template import RequestContext, Context
from django.forms.formsets import formset_factory, BaseFormSet
from django.http import HttpResponse, HttpResponseRedirect
from qtool.forms import *
from qtool.models import *


def index(request):
	
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = False
	
	VariableFormSet = formset_factory(VariableForm, max_num = 10, formset = RequiredFormSet)
	ChoiceFormSet = formset_factory(ChoiceForm, max_num = 10, formset = RequiredFormSet)
	HintFormSet = formset_factory(HintForm, max_num = 10, formset = RequiredFormSet)
	if request.method == 'POST':
		problem_form = ProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST)
		answer_form = AnswerForm(request.POST)

		variable_formset = VariableFormSet(request.POST, request.FILES)
		choice_formset = ChoiceFormSet(request.POST, request.FILES)
		
		hint_formset = HintFormSet(request.POST, request.FILES)

		if problem_form.is_valid() and problem_template_form.is_valid() and variable_formset.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and answer_form.is_valid():
			
			problem = problem_form.save()
			problem_template = problem_template_form.save(commit = False)
			problem_template.problem = problem
			problem_template.save()

			answer = answer_form.save(commit = False)
			answer.problem = problem
			answer.save()

			for form in variable_formset.forms:
				variable = form.save(commit = False)
				variable.problem = problem
				variable.save()

			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save()

			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()


			return HttpResponseRedirect('success')

	else:
		problem_form = ProblemForm()
		problem_template_form = ProblemTemplateForm()
		answer_form = AnswerForm()
		variable_formset = VariableFormSet()
		choice_formset = ChoiceFormSet()
		hint_formset = HintFormSet()

	c = {'problem_form' : problem_form,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'variable_formset' : variable_formset,
	     'choice_formset' : choice_formset,
	     'hint_formset' : hint_formset,
	    }

	c.update(csrf(request))
	return render_to_response('qtool/index.html',c)




def problems(request):
	problems = Problem.objects.all()
	context = Context({'problems':problems})
	return render_to_response('qtool/problems.html', context)


def details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()
	context = Context({
		'p':p,
		'title':p.title,	
		
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('qtool/details.html', context)








