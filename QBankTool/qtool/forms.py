from qtool.models import *
from django.forms import ModelForm

from django import forms


class VariableForm(ModelForm):
	class Meta:
		model = Variable
		widgets = {
          		'var_value': forms.Textarea(attrs={'rows':2, 'cols':20}),
        	}
		
		exclude = ('problem',)


class ProblemTemplateForm(ModelForm):
        class Meta:
	        model = ProblemTemplate
		widgets = {
          		'question': forms.Textarea(attrs={'rows':2, 'cols':20}),
        	}
		exclude = ('problem',)


class AnswerForm(ModelForm):
        class Meta:
	        model = Answer
		widgets = {
          		'solution': forms.Textarea(attrs={'rows':2, 'cols':20}),
        	}
		exclude = ('problem',)


class ChoiceForm(ModelForm):
        class Meta:
	        model = Choice
		widgets = {
          		'choice': forms.Textarea(attrs={'rows':2, 'cols':20}),
        	}
		exclude = ('problem',)


class HintForm(ModelForm):
        class Meta:
	        model = Hint
		widgets = {
          		'hint': forms.Textarea(attrs={'rows':2, 'cols':20}),
        	}
		exclude = ('problem',)


class ProblemForm(ModelForm):
	class Meta:
		model = Problem
	
