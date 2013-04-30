from qtool.models import *
from django.forms import ModelForm

from django import forms

class VariableForm(ModelForm):
	class Meta:
		model = Variable
		widgets = {
			'var_value': forms.Textarea(attrs={'rows': 2, 'cols': 20}),
			'attribute': forms.Textarea(attrs={'class' : 'myfieldclass', 'required' : 'False'}),
			
			}
		exclude = ('problem',)

class CommonIntroductionForm(ModelForm):
	class Meta:
		model = CommonIntroduction
		widgets = {
          		'common_intro': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)

class ProblemTemplateForm(ModelForm):
	class Meta:
		model = ProblemTemplate
		widgets = {
          		'question': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)


class AnswerForm(ModelForm):
	class Meta:
		model = Answer
		widgets = {
          		'solution': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)


class ChoiceForm(ModelForm):
	class Meta:
		model = Choice
		widgets = {
          		'choice': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)


class HintForm(ModelForm):
	class Meta:
		model = Hint
		widgets = {
          		'hint': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)

class ScriptForm(ModelForm):
	class Meta:
		model = Script
		widgets = {
          		'script': forms.Textarea(attrs={'class' : 'myfieldclass'}),
        	}
		exclude = ('problem',)

class ProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='KA')
	class Meta:
		model = Problem
		


class SimpleProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='Simple')
	class Meta:
		model = Problem
		exclude = ('variable','script')

class ListProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='List')
	class Meta:
		model = Problem
		exclude = ('script', 'choice','hint')


class RangeProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='Range')
	class Meta:
		model = Problem
		exclude = ('script', 'choice','hint')


		
class SummativeProblemForm(ModelForm):
	type = forms.CharField(widget = forms.HiddenInput(), initial ='Summative')
	class Meta:
		model = Problem
		exclude = ('script', 'choice','hint', 'answer', 'variable')
