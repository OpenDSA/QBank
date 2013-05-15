# Create your views here.

from django.shortcuts import render_to_response, get_object_or_404
from django.core.context_processors import csrf
from django.template import RequestContext, loader,Context
from django.forms.formsets import formset_factory, BaseFormSet
from django.forms.models import inlineformset_factory
from django.http import HttpResponse, HttpResponseRedirect
from django.contrib.auth.decorators import login_required
from qtool.forms import *
from qtool.models import *

import csv


@login_required
def index(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	HintFormSet = formset_factory(HintForm, max_num = 10, formset = RequiredFormSet)
	VariableFormSet = formset_factory(VariableForm, max_num = 10, formset = RequiredFormSet)
	ChoiceFormSet = formset_factory(ChoiceForm, max_num = 10, formset = RequiredFormSet)
	ScriptFormSet = formset_factory(ScriptForm, max_num = 10, formset = RequiredFormSet)


	if request.method == 'POST': # If the form has been submitted...
		problem_form = ProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')

		hint_formset = HintFormSet(request.POST, request.FILES, prefix='hints')
		variable_formset = VariableFormSet(request.POST, request.FILES, prefix='variables')
		choice_formset = ChoiceFormSet(request.POST, request.FILES, prefix='choices')
		script_formset = ScriptFormSet(request.POST, request.FILES, prefix='scripts')
		if problem_form.is_valid() and problem_template_form.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and variable_formset.is_valid() and answer_form.is_valid() and script_formset.is_valid():
			problem = problem_form.save()
			problem_template = problem_template_form.save(commit = False)
			problem_template.problem = problem
			problem_template.save()

			answer = answer_form.save(commit = False)
			answer.problem = problem
			answer.save()
			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()
			for form in variable_formset.forms:
				variable = form.save(commit = False)
				variable.problem = problem
				variable.save()
			for form in script_formset.forms:
				script = form.save(commit = False)
				script.problem = problem
				script.save()
			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qtool/problems/')
	else:	
		problem_form = ProblemForm()
		choice_formset = ChoiceFormSet(prefix='choices')
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')
		script_formset = ScriptFormSet(prefix='scripts')
		variable_formset = VariableFormSet(prefix='variables')
		hint_formset = HintFormSet(prefix='hints')

	c = {'problem_form' : problem_form,
	     'choice_formset' : choice_formset,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'variable_formset' : variable_formset,
	     'script_formset' : script_formset,
	     'hint_formset' : hint_formset,
	}
	c.update(csrf(request))
	return render_to_response('qtool/add.html', c)

@login_required
def edit(request, problem_id):

	problem = get_object_or_404(Problem, id=problem_id)

	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	
	class SensibleFormset(BaseFormSet):
		def total_form_count(self):
			"""Returns the total number of forms in this FormSet."""
			if self.data or self.files:
				return self.management_form.cleaned_data[TOTAL_FORM_COUNT]
			else:
				if self.initial_form_count() > 0:
                			total_forms = self.initial_form_count()
				else:
                			total_forms = self.initial_form_count() + self.extra
				if total_forms > self.max_num > 0:
                			total_forms = self.max_num
				return total_forms
        		
	
	ProblemTemplateInlineFormSet = inlineformset_factory(Problem, ProblemTemplate)
	
	AnswerInlineFormSet = inlineformset_factory(Problem, Answer, max_num =1)
	VariableInlineFormSet = inlineformset_factory(Problem, Variable)
	CommonIntroductionFormSet =  inlineformset_factory(Problem, CommonIntroduction, max_num =1 )
	ChoiceInlineFormSet = inlineformset_factory(Problem, Choice,)

	HintInlineFormSet = inlineformset_factory(Problem, Hint)
		
	ScriptInlineFormSet = inlineformset_factory(Problem, Script)
	if request.method == 'POST':
		problem_form =ProblemForm(request.POST, instance=problem)
		common_introduction_formset = CommonIntroductionForm(request.POST, instance=problem, prefix='common_intro')
		problem_template_formset = ProblemTemplateInlineFormSet(request.POST,request.FILES, instance=problem, prefix='templates')
		answer_formset = AnswerInlineFormSet(request.POST, instance=problem, prefix='answer')
		hint_formset = HintInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='hints')
		choice_formset = ChoiceInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='choices')
		script_formset = ScriptInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='scripts')
		variable_formset = VariableInlineFormSet(request.POST, request.FILES, instance=problem,  prefix='variables')
		if problem_form.is_valid() and variable_formset.is_valid() and problem_template_formset.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and answer_formset.is_valid() and script_formset.is_valid() and common_introduction_formset.is_valid():
			problem = problem_form.save()
			problem_template_formset.save()
			answer_formset.save()
			common_introduction_formset.save()
			
			variable_formset.save()
			hint_formset.save()
			script_formset.save()
			choice_formset.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qtool/problems/')
	else:
		problem_form = ProblemForm(instance=problem)
		choice_formset = ChoiceInlineFormSet(instance = problem, prefix='choices')
		problem_template_formset = ProblemTemplateInlineFormSet(instance = problem, prefix='templates')
		answer_formset = AnswerInlineFormSet(instance = problem, prefix='answer')
		script_formset = ScriptInlineFormSet(instance=problem, prefix='scripts')
		variable_formset = VariableInlineFormSet(instance = problem, prefix='variables')
		common_introduction_formset = CommonIntroductionFormSet(instance = problem, prefix='common_intro')
		hint_formset = HintInlineFormSet(instance=problem, prefix='hints')
	c = {
	'problem_form' : problem_form,
	'choice_formset' : choice_formset,
	'problem_template_formset' :problem_template_formset,
	'answer_formset': answer_formset,
	'variable_formset' : variable_formset,
	'script_formset' : script_formset,
	'common_introduction_formset' : common_introduction_formset,
	'hint_formset' : hint_formset,
    	}
	c.update(csrf(request))
	return render_to_response('qtool/edit.html', c)

def splashpage(request):
	return HttpResponseRedirect('splashpage')


@login_required
def problems(request):
	problems = Problem.objects.all()
	context = Context({'problems':problems})
	return render_to_response('qtool/problems.html', context)


@login_required
def problems_Summary(request):
	problems = Problem.objects.all()
	context = Context({'problems':problems})
	return render_to_response('qtool/problems_Summary.html', context)


@login_required
def ka_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()

	destination = open('/quiz/qtool/media/exercises/'+p.title+'.html', 'wb+')
	str ="<!DOCTYPE html><html data-require=\"math math-format word-problems spin\"><head>"+"\n"+"<title>"+"\n"+p.title+"</title><script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\"></script><script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script><script>urlBaseOverride = \"../qtool/\";</script><script src=\"../qtool/khan-exercise.js\"></script><script type=\"text/javascript\"src=\"http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML\"></script>"
	for scr in p.script_set.all():
		str += "<p>"
		str += scr.script
		str += "</p>"



	str += "</head><body><div class=\"exercise\"><div class=\"problems\"><div id=\"1\"><div class=\"vars\">"

	for t in p.variable_set.all():
		str +="<var id=\""
		str += t.var_id
		str += "\">"	
		str += t.var_value
		str += "</var>"


	str += "</div> <div class=\"question\">"
	str += q.question
	str += "</div>"
	str += "<div class=\"solution\"><var>"
	str += s.solution
	str += "</var></div>"
	str += "<ul class =\"choices\">"
	for c in p.choice_set.all():
		str += "<li><var>"
		str += c.choice
		str += "</var></li>"
	str += "</ul>"
	str += "<div class=\"hints\">"
	for h in p.hint_set.all():
		str += h.hint
	str += "</div>"
	str += "</div></div></div></body></html>"	
	destination.write(bytes(str,'UTF-8'))
	destination.close()


	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('qtool/ka_details.html', context)

@login_required
def simple_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()
	destination = open('/quiz/qtool/media/exercises/'+p.title+'.html', 'wb+')
	
	str ="<!DOCTYPE html><html data-require=\"math\"><head>"+"\n"+"<title>"+"\n"+p.title+"</title><script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\"></script><script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script><script>urlBaseOverride = \"../qtool/\";</script><script src=\"../qtool/khan-exercise.js\"></script></head><body><div class=\"exercise\"><div class=\"vars\"></div><div class=\"problems\"><div id=\"problem-type-or-description\"><p class=\"question\">"
	str += q.question
	str += "</p>"
	str += "<div class=\"solution\"><var>\""
	str += s.solution
	str += "\"</var></div>"
	str += "<ul class =\"choices\">"
	for c in p.choice_set.all():
		str += "<li><var>\""
		str += c.choice
		str += "\"</var></li>"
	str += "</ul>"
	str += "<div class=\"hints\">"
	for h in p.hint_set.all():
		str += "<p>\""
		str += h.hint
		str += "\"</p>"
	str += "</div>"
	str += "</div></div></div></body></html>"	

	destination.write(bytes(str,'UTF-8'))
	destination.close()

	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('qtool/simple_details.html', context)


@login_required
def summative_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.filter(problem = p)
#	v = Variable.objects.get(problem = p)
#	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
#	h = p.hint_set.all()
	destination = open('/quiz/qtool/media/exercises/'+p.title+'.html', 'wb+')

	str ="<!DOCTYPE html><html data-require=\"math\"><head>"+"\n"+"<title>"+"\n"+p.title+"</title><script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\"></script><script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script><script>urlBaseOverride = \"../qtool/\";</script><script src=\"../qtool/khan-exercise.js\"></script></head><body>"
	
	for c in p.problemtemplate_set.all():
		str += "<div class=\"exercise\" data-name=\""
		str += c.question
		str += "\">"
		str += "</div>"
	str +="</body></html>"
	destination.write(bytes(str,'UTF-8'))
	destination.close()


	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
	#	'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('qtool/summative_details.html', context)


@login_required
def ka_gen(request, problem_id):

	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.filter(problem = p)
#	v = Variable.objects.get(problem = p)
#	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
#	h = p.hint_set.all()
	destination = open('/quiz/qtool/media/exercises/'+p.title+'_View.html', 'wb+')

	str ="<!DOCTYPE html><html data-require=\"math\"><head>"+"\n"+"<title>"+"\n"+p.title+"</title><script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\"></script><script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script><script>urlBaseOverride = \"../qtool/\";</script><script src=\"../qtool/khan-exercise.js\"></script></script></head><body>"
	
	
	str += "<div class=\"exercise\" data-name=\""
	str += p.title
	str += "\">"
	str += "</div>"
	str +="</body></html>"
	destination.write(bytes(str,'UTF-8'))
	destination.close()


	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
	#	'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('qtool/ka_gen.html', context)


@login_required
def range_details(request, problem_id):
	p = get_object_or_404(Problem, id=problem_id)
	q = ProblemTemplate.objects.get(problem = p)
#	v = Variable.objects.get(problem = p)
	s = Answer.objects.get(problem = p)
#	c = Choice.objects.get(problem = p)
	h = p.hint_set.all()
	destination = open('/quiz/qtool/media/exercises/'+p.title+'.html', 'wb+')

	str ="<!DOCTYPE html><html data-require=\"math\"><head>"+"\n"+"<title>"+"\n"+p.title+"</title><script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\"></script><script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script><script>urlBaseOverride = \"../qtool/\";</script><script src=\"../qtool/khan-exercise.js\"></script></head><body><div class=\"exercise\"><div class=\"vars\">" 
	for t in p.variable_set.all():
		str +="<var id=\""
		str += t.var_id
		str += "\">"	
		str += t.var_value
		str += "</var>"

	str += "</div><div class=\"problems\"> <div id=\"problem-type-or-description\"><p class=\"problem\"><p class=\"question\">"
	str += q.question
	str += "</p><div class=\"solution\"><var>"
	str += s.solution
	str += "</var></div></div></div></div></body></html>"

	destination.write(bytes(str,'UTF-8'))

	destination.close()

	context = Context({
		'p':p,
		'title':p.title,	
		'question':q,
		'solution':s,
	#	'choice':c,
#		'hint':h
	})
	return render_to_response('qtool/range_details.html', context)


@login_required
def list_details(request, problem_id):
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
	return render_to_response('qtool/simple_details.html', context)

@login_required
def write_file(request, problem_id):
	response = HttpResponse( content_type = 'text/csv')
	p = get_object_or_404(Problem, id=problem_id)
	response['Content-Disposition'] = 'attachment; filename="'+p.title+'.csv"'

	writer = csv.writer(response)
	problems = Problem.objects.filter(id = problem_id)
	
	for p in problems:
		writer.writerow(['TITLE',p.title])
		writer.writerow(['DIFFICULTY',p.difficulty_level])
		q = ProblemTemplate.objects.filter(problem = p)

		for t in q:
			writer.writerow(['QUESTION', t.question])

		for t in p.variable_set.all():
			writer.writerow(['VAR_NAME', t.var_id])
			writer.writerow(['VAR_VALUE', t.var_value])
			writer.writerow(['ATTR_INFO',t.attribute])
	try:
		c = CommonIntroduction.objects.get(problem = p)
		writer.writerow(['COMMON_INTRO', c.common_intro])
	except CommonIntroduction.DoesNotExist:
  		c = None
	try:
		s = Answer.objects.get(problem = p)
		writer.writerow(['SOLUTION', s.solution])
	except Answer.DoesNotExist:
		s = None
		for t in p.script_set.all():
			writer.writerow(['SCRIPT',t.script])
		for t in p.choice_set.all():
			writer.writerow(['CHOICE', t.choice])
		for t in p.hint_set.all():
			writer.writerow(['HINT', t.hint])
	return response

@login_required
def delete(request, problem_id):
   p= Problem.objects.get(id = problem_id)
   p.delete()
   return HttpResponseRedirect('/qtool/problems/')

@login_required
def simple(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	HintFormSet = formset_factory(HintForm, max_num = 10, formset = RequiredFormSet)
	ChoiceFormSet = formset_factory(ChoiceForm, max_num = 10, formset = RequiredFormSet)


	if request.method == 'POST': # If the form has been submitted...
		problem_form = SimpleProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')

		hint_formset = HintFormSet(request.POST, request.FILES, prefix='hints')
		
		choice_formset = ChoiceFormSet(request.POST, request.FILES, prefix='choices')
		if problem_form.is_valid() and problem_template_form.is_valid() and choice_formset.is_valid() and hint_formset.is_valid() and answer_form.is_valid():
			problem = problem_form.save()
			problem_template = problem_template_form.save(commit = False)
			problem_template.problem = problem
			problem_template.save()

			answer = answer_form.save(commit = False)
			answer.problem = problem
			answer.save()
			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()
			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qtool/problems/')
	else:
		problem_form = SimpleProblemForm()
		choice_formset = ChoiceFormSet(prefix='choices')
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')
		hint_formset = HintFormSet(prefix='hints')
	c = {'problem_form' : problem_form,
	     'choice_formset' : choice_formset,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'hint_formset' : hint_formset,
	}
	c.update(csrf(request))
	return render_to_response('qtool/simple.html', c)


@login_required
def list(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	VariableFormSet = formset_factory(VariableForm, max_num = 10, formset = RequiredFormSet)
	HintFormSet = formset_factory(HintForm, max_num = 10, formset = RequiredFormSet)
	ChoiceFormSet = formset_factory(ChoiceForm, max_num = 10, formset = RequiredFormSet)


	if request.method == 'POST': # If the form has been submitted...
		problem_form = ListProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')
		variable_formset = VariableFormSet(request.POST,request.FILES, prefix='variables')
		hint_formset = HintFormSet(request.POST, request.FILES, prefix='hints')
		choice_formset = ChoiceFormSet(request.POST, request.FILES, prefix='choices')
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
				variable.save() # Redirect to a 'success' page
			for form in hint_formset.forms:
				hint = form.save(commit = False)
				hint.problem = problem
				hint.save()
			for form in choice_formset.forms:
				choice = form.save(commit = False)
				choice.problem = problem
				choice.save()
			return HttpResponseRedirect('/qtool/problems/')
	else:
		problem_form = ProblemForm()
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')	
		variable_formset = VariableFormSet(prefix='variables')
		choice_formset = ChoiceFormSet(prefix='choices')
		hint_formset = HintFormSet(prefix='hints')
	c = {'problem_form' : problem_form,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'variable_formset' : variable_formset,
	     'choice_formset': choice_formset,
	     'hint_formset' : hint_formset,
	}
	c.update(csrf(request))
	return render_to_response('qtool/list.html', c)


@login_required
def range(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True
	VariableFormSet = formset_factory(VariableForm, max_num = 10, formset = RequiredFormSet)
	if request.method == 'POST': # If the form has been submitted...
		problem_form = RangeProblemForm(request.POST)
		problem_template_form = ProblemTemplateForm(request.POST, prefix='template')
		answer_form = AnswerForm(request.POST, prefix='answer')
		variable_formset = VariableFormSet(request.POST,request.FILES, prefix='variables')
		if problem_form.is_valid() and problem_template_form.is_valid() and variable_formset.is_valid() and answer_form.is_valid():
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
				variable.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qtool/problems/')
	else:
		problem_form = RangeProblemForm()
		problem_template_form = ProblemTemplateForm(prefix='template')
		answer_form = AnswerForm(prefix='answer')	
		variable_formset = VariableFormSet(prefix='variables')
	c = {'problem_form' : problem_form,
	     'problem_template_form' : problem_template_form,
	     'answer_form': answer_form,
	     'variable_formset' : variable_formset,
	}
	c.update(csrf(request))
	return render_to_response('qtool/range.html', c)




@login_required
def summative(request):
    # This class is used to make empty formset forms required
    # See http://stackoverflow.com/questions/2406537/django-formsets-make-first-required/4951032#4951032
	class RequiredFormSet(BaseFormSet):
		def __init__(self, *args, **kwargs):
			super(RequiredFormSet, self).__init__(*args, **kwargs)
			for form in self.forms:
				form.empty_permitted = True

	problems = Problem.objects.all()
	ProblemTemplateFormSet = formset_factory(ProblemTemplateForm, max_num = 10, formset = RequiredFormSet)
	if request.method == 'POST': # If the form has been submitted...
		problem_form = SummativeProblemForm(request.POST)
		common_introduction_form = CommonIntroductionForm(request.POST, prefix='common_intro')
		problemtemplate_formset = ProblemTemplateFormSet(request.POST, request.FILES, prefix='templates')
		if problem_form.is_valid() and common_introduction_form.is_valid() and problemtemplate_formset.is_valid():
			problem = problem_form.save()
			common_intro = common_introduction_form.save(commit=False)
			common_intro.problem = problem
			common_intro.save()

			for form in problemtemplate_formset.forms:
				problem_template = form.save(commit=False)
				problem_template.problem = problem
				problem_template.save() # Redirect to a 'success' page
			return HttpResponseRedirect('/qtool/problems/')
	else:
		problem_form = SummativeProblemForm()
		common_introduction_form = CommonIntroductionForm(prefix='common_intro')
		problemtemplate_formset = ProblemTemplateFormSet(prefix='templates')
	c = {'problem_form' : problem_form,
		'common_introduction_form' : common_introduction_form,
		'problem_template_formset' : problemtemplate_formset,
		'problems':problems,
	}
	c.update(csrf(request))
	return render_to_response('qtool/summative.html', c)



